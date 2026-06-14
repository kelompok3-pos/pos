<?php

final class ExcelExporter
{
    public static function download(
        string $filename,
        string $title,
        array $columns,
        array $rows,
        array $metadata = []
    ): never {
        $path = tempnam(sys_get_temp_dir(), 'pos-xlsx-');
        if ($path === false) {
            throw new RuntimeException('Tidak dapat membuat file Excel sementara.');
        }

        try {
            self::write($path, $title, $columns, $rows, $metadata);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . self::safeFilename($filename) . '"');
            header('Content-Length: ' . filesize($path));
            header('Cache-Control: max-age=0, must-revalidate');
            readfile($path);
        } finally {
            @unlink($path);
        }

        exit;
    }

    public static function write(
        string $path,
        string $title,
        array $columns,
        array $rows,
        array $metadata = []
    ): void {
        if ($columns === []) {
            throw new InvalidArgumentException('Kolom Excel tidak boleh kosong.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat file Excel.');
        }

        $sheet = self::worksheet($title, $columns, $rows, $metadata);
        $hasTable = $rows !== [];

        $zip->addFromString('[Content_Types].xml', self::contentTypes($hasTable));
        $zip->addFromString('_rels/.rels', self::packageRelationships());
        $zip->addFromString('docProps/app.xml', self::appProperties());
        $zip->addFromString('docProps/core.xml', self::coreProperties());
        $zip->addFromString('xl/workbook.xml', self::workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRelationships());
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet['xml']);

        if ($hasTable) {
            $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', self::sheetRelationships());
            $zip->addFromString(
                'xl/tables/table1.xml',
                self::table($columns, $sheet['headerRow'], $sheet['lastRow'])
            );
        }

        $zip->close();
    }

    private static function worksheet(string $title, array $columns, array $rows, array $metadata): array
    {
        $columnCount = count($columns);
        $lastColumn = self::columnName($columnCount);
        $rowNumber = 1;
        $xmlRows = [];

        $xmlRows[] = self::row($rowNumber, [
            self::inlineCell('A' . $rowNumber, $title, 1),
        ], 28);
        $rowNumber++;

        foreach ($metadata as $label => $value) {
            $xmlRows[] = self::row($rowNumber, [
                self::inlineCell('A' . $rowNumber, (string) $label . ': ' . (string) $value, 2),
            ]);
            $rowNumber++;
        }

        $rowNumber++;
        $headerRow = $rowNumber;
        $headerCells = [];
        foreach (array_values($columns) as $index => $column) {
            $headerCells[] = self::inlineCell(
                self::columnName($index + 1) . $headerRow,
                (string) $column['label'],
                3
            );
        }
        $xmlRows[] = self::row($headerRow, $headerCells, 22);

        foreach ($rows as $row) {
            $rowNumber++;
            $cells = [];
            foreach (array_values($columns) as $index => $column) {
                $reference = self::columnName($index + 1) . $rowNumber;
                $cells[] = self::dataCell(
                    $reference,
                    $row[$column['key']] ?? null,
                    $column['type'] ?? 'string',
                    !empty($row['__total'])
                );
            }
            $xmlRows[] = self::row($rowNumber, $cells);
        }

        $lastRow = max($headerRow, $rowNumber);
        $columnsXml = '';
        foreach (array_values($columns) as $index => $column) {
            $position = $index + 1;
            $width = (float) ($column['width'] ?? 18);
            $columnsXml .= '<col min="' . $position . '" max="' . $position . '" width="' . $width . '" customWidth="1"/>';
        }

        $mergedRows = range(1, max(1, $headerRow - 2));
        $mergeCells = '';
        foreach ($mergedRows as $mergedRow) {
            $mergeCells .= '<mergeCell ref="A' . $mergedRow . ':' . $lastColumn . $mergedRow . '"/>';
        }

        $autoFilter = $rows === []
            ? '<autoFilter ref="A' . $headerRow . ':' . $lastColumn . $headerRow . '"/>'
            : '';
        $tableParts = $rows !== []
            ? '<tableParts count="1"><tablePart r:id="rId1"/></tableParts>'
            : '';

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="' . $headerRow . '" topLeftCell="A'
            . ($headerRow + 1) . '" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15"/>'
            . '<cols>' . $columnsXml . '</cols>'
            . '<sheetData>' . implode('', $xmlRows) . '</sheetData>'
            . $autoFilter
            . '<mergeCells count="' . count($mergedRows) . '">' . $mergeCells . '</mergeCells>'
            . '<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
            . '<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/>'
            . $tableParts
            . '</worksheet>';

        return ['xml' => $xml, 'headerRow' => $headerRow, 'lastRow' => $lastRow];
    }

    private static function dataCell(string $reference, mixed $value, string $type, bool $total): string
    {
        if ($value === null || $value === '') {
            return '<c r="' . $reference . '" s="' . ($total ? 8 : 0) . '"/>';
        }

        $style = match ($type) {
            'integer' => $total ? 9 : 4,
            'number' => $total ? 10 : 5,
            'currency' => $total ? 11 : 6,
            'date' => $total ? 14 : 13,
            'datetime' => $total ? 12 : 7,
            default => $total ? 8 : 0,
        };

        if (in_array($type, ['integer', 'number', 'currency'], true)) {
            return '<c r="' . $reference . '" s="' . $style . '"><v>' . (float) $value . '</v></c>';
        }

        if (in_array($type, ['date', 'datetime'], true)) {
            try {
                $date = new DateTimeImmutable((string) $value);
                $base = new DateTimeImmutable('1899-12-30 00:00:00', $date->getTimezone());
                $excelDate = (int) $base->diff($date)->format('%r%a');
                if ($type === 'datetime') {
                    $excelDate += (
                        ((int) $date->format('H') * 3600)
                        + ((int) $date->format('i') * 60)
                        + (int) $date->format('s')
                    ) / 86400;
                }
                return '<c r="' . $reference . '" s="' . $style . '"><v>' . $excelDate . '</v></c>';
            } catch (Exception) {
                // Keep invalid date values readable instead of failing the complete export.
            }
        }

        return self::inlineCell($reference, (string) $value, $style);
    }

    private static function inlineCell(string $reference, string $value, int $style): string
    {
        return '<c r="' . $reference . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">'
            . self::escape($value) . '</t></is></c>';
    }

    private static function row(int $number, array $cells, ?int $height = null): string
    {
        $heightAttribute = $height === null ? '' : ' ht="' . $height . '" customHeight="1"';
        return '<row r="' . $number . '"' . $heightAttribute . '>' . implode('', $cells) . '</row>';
    }

    private static function table(array $columns, int $headerRow, int $lastRow): string
    {
        $lastColumn = self::columnName(count($columns));
        $tableColumns = '';
        foreach (array_values($columns) as $index => $column) {
            $tableColumns .= '<tableColumn id="' . ($index + 1) . '" name="'
                . self::escape((string) $column['label']) . '"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<table xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" id="1" name="ReportTable" '
            . 'displayName="ReportTable" ref="A' . $headerRow . ':' . $lastColumn . $lastRow . '" totalsRowShown="0">'
            . '<autoFilter ref="A' . $headerRow . ':' . $lastColumn . $lastRow . '"/>'
            . '<tableColumns count="' . count($columns) . '">' . $tableColumns . '</tableColumns>'
            . '<tableStyleInfo name="TableStyleMedium2" showFirstColumn="0" showLastColumn="0" '
            . 'showRowStripes="1" showColumnStripes="0"/></table>';
    }

    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="4"><numFmt numFmtId="164" formatCode="#,##0.00"/>'
            . '<numFmt numFmtId="165" formatCode="&quot;Rp&quot; #,##0.00"/>'
            . '<numFmt numFmtId="166" formatCode="dd/mm/yyyy hh:mm"/>'
            . '<numFmt numFmtId="167" formatCode="dd/mm/yyyy"/></numFmts>'
            . '<fonts count="5"><font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="16"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            . '<font><i/><sz val="10"/><color rgb="FF475569"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FF0F172A"/><name val="Calibri"/></font></fonts>'
            . '<fills count="4"><fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF1E3A5F"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFDCE6F1"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border><left style="thin"><color rgb="FFB8C4D0"/></left><right style="thin"><color rgb="FFB8C4D0"/></right>'
            . '<top style="thin"><color rgb="FFB8C4D0"/></top><bottom style="thin"><color rgb="FFB8C4D0"/></bottom><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="15">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>'
            . '<xf numFmtId="1" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '<xf numFmtId="165" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '<xf numFmtId="166" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '<xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>'
            . '<xf numFmtId="1" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1"/>'
            . '<xf numFmtId="164" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1"/>'
            . '<xf numFmtId="165" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1"/>'
            . '<xf numFmtId="166" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1"/>'
            . '<xf numFmtId="167" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '<xf numFmtId="167" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1"/>'
            . '</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private static function contentTypes(bool $hasTable): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . ($hasTable ? '<Override PartName="/xl/tables/table1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.table+xml"/>' : '')
            . '</Types>';
    }

    private static function packageRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private static function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private static function workbookRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private static function sheetRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/table" Target="../tables/table1.xml"/>'
            . '</Relationships>';
    }

    private static function appProperties(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
            . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>POS App</Application></Properties>';
    }

    private static function coreProperties(): string
    {
        $now = gmdate('Y-m-d\TH:i:s\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            . 'xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>POS App</dc:creator><cp:lastModifiedBy>POS App</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified></cp:coreProperties>';
    }

    private static function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }
        return $name;
    }

    private static function escape(string $value): string
    {
        $value = preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value) ?? '';
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function safeFilename(string $filename): string
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '-', $filename) ?: 'laporan.xlsx';
        return str_ends_with(strtolower($filename), '.xlsx') ? $filename : $filename . '.xlsx';
    }
}
