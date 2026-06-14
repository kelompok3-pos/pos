<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$pdo = getConnection();
$lockName = 'pos_schema_migrations_' . (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
$lock = $pdo->prepare('SELECT GET_LOCK(?, 10)');
$lock->execute([$lockName]);
if ((int) $lock->fetchColumn() !== 1) {
    fwrite(STDERR, "Could not acquire migration lock.\n");
    exit(1);
}
register_shutdown_function(static function () use ($pdo, $lockName): void {
    $release = $pdo->prepare('SELECT RELEASE_LOCK(?)');
    $release->execute([$lockName]);
});

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

$mode = $argv[1] ?? 'migrate';
if (!in_array($mode, ['migrate', '--baseline', '--status'], true)) {
    fwrite(STDERR, "Usage: php database/migrate.php [--status|--baseline]\n");
    exit(2);
}

$files = glob(__DIR__ . '/migrations/*.sql') ?: [];
sort($files, SORT_STRING);
$applied = array_fill_keys(
    $pdo->query('SELECT filename FROM migrations ORDER BY filename')->fetchAll(PDO::FETCH_COLUMN),
    true
);
$record = $pdo->prepare('INSERT INTO migrations (filename) VALUES (?)');

foreach ($files as $file) {
    $filename = basename($file);
    if (isset($applied[$filename])) {
        echo "SKIPPED {$filename}\n";
        continue;
    }
    if ($mode === '--status') {
        echo "PENDING {$filename}\n";
        continue;
    }
    if ($mode === '--baseline') {
        $record->execute([$filename]);
        echo "BASELINED {$filename}\n";
        continue;
    }

    try {
        foreach (splitSqlStatements((string) file_get_contents($file)) as $statement) {
            $pdo->exec($statement);
        }
        $record->execute([$filename]);
        echo "APPLIED {$filename}\n";
    } catch (Throwable $exception) {
        fwrite(STDERR, "FAILED {$filename}: {$exception->getMessage()}\n");
        exit(1);
    }
}

function splitSqlStatements(string $sql): array
{
    $statements = [];
    $buffer = '';
    $quote = null;
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $next = $i + 1 < $length ? $sql[$i + 1] : '';

        if ($quote === null && $char === '-' && $next === '-' && ($i + 2 >= $length || ctype_space($sql[$i + 2]))) {
            while ($i < $length && $sql[$i] !== "\n") {
                $i++;
            }
            $buffer .= "\n";
            continue;
        }
        if ($quote === null && $char === '#') {
            while ($i < $length && $sql[$i] !== "\n") {
                $i++;
            }
            $buffer .= "\n";
            continue;
        }
        if ($quote === null && $char === '/' && $next === '*') {
            $i += 2;
            while ($i + 1 < $length && !($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                $i++;
            }
            $i++;
            continue;
        }
        if ($char === '\\' && $quote !== null && $i + 1 < $length) {
            $buffer .= $char . $sql[++$i];
            continue;
        }
        if ($char === "'" || $char === '"' || $char === '`') {
            if ($quote === null) {
                $quote = $char;
            } elseif ($quote === $char) {
                if ($i + 1 < $length && $sql[$i + 1] === $char) {
                    $buffer .= $char . $sql[++$i];
                    continue;
                }
                $quote = null;
            }
        }
        if ($char === ';' && $quote === null) {
            if (trim($buffer) !== '') {
                $statements[] = trim($buffer);
            }
            $buffer = '';
            continue;
        }
        $buffer .= $char;
    }

    if (trim($buffer) !== '') {
        $statements[] = trim($buffer);
    }
    return $statements;
}
