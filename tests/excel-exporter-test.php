<?php

require_once __DIR__ . '/../src/Support/ExcelExporter.php';

function failExcelTest(string $message): never
{
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

function validateWorkbook(string $path, bool $expectsTable): void
{
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        failExcelTest('Generated workbook is not a readable ZIP archive.');
    }

    $required = [
        '[Content_Types].xml',
        '_rels/.rels',
        'xl/workbook.xml',
        'xl/styles.xml',
        'xl/worksheets/sheet1.xml',
    ];
    foreach ($required as $entry) {
        if ($zip->locateName($entry) === false) {
            failExcelTest("Workbook is missing {$entry}.");
        }
    }

    if (($zip->locateName('xl/tables/table1.xml') !== false) !== $expectsTable) {
        failExcelTest('Workbook table structure does not match its data rows.');
    }

    for ($index = 0; $index < $zip->numFiles; $index++) {
        $name = $zip->getNameIndex($index);
        if ($name !== false && str_ends_with($name, '.xml')) {
            libxml_use_internal_errors(true);
            if (simplexml_load_string($zip->getFromIndex($index)) === false) {
                failExcelTest("Workbook contains invalid XML in {$name}.");
            }
        }
    }

    $zip->close();
}

date_default_timezone_set('Asia/Jakarta');

$populated = tempnam(sys_get_temp_dir(), 'pos-xlsx-test-');
$empty = tempnam(sys_get_temp_dir(), 'pos-xlsx-test-');
if ($populated === false || $empty === false) {
    failExcelTest('Could not create temporary test files.');
}

try {
    $columns = [
        ['key' => 'name', 'label' => 'Nama', 'width' => 24],
        ['key' => 'amount', 'label' => 'Nominal', 'type' => 'currency', 'width' => 18],
        ['key' => 'date', 'label' => 'Tanggal', 'type' => 'date', 'width' => 16],
    ];
    ExcelExporter::write(
        $populated,
        'Laporan Pengujian',
        $columns,
        [['name' => 'Item A', 'amount' => 125000, 'date' => '2026-06-15']],
        ['Periode' => 'Juni 2026']
    );
    ExcelExporter::write($empty, 'Laporan Kosong', $columns, []);

    validateWorkbook($populated, true);
    validateWorkbook($empty, false);

    $zip = new ZipArchive();
    $zip->open($populated);
    if (!str_contains($zip->getFromName('xl/worksheets/sheet1.xml'), '<v>46188</v>')) {
        failExcelTest('Local calendar date shifted during Excel date conversion.');
    }
    $zip->close();
} finally {
    @unlink($populated);
    @unlink($empty);
}

echo "PASS: Excel exporter generated valid populated and empty workbooks.\n";
