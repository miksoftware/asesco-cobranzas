<?php

/**
 * Script para transferir todos los datos desde SQLite a MySQL (Laragon).
 * Ejecución: php database/migrate_sqlite_to_mysql.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbName = env('DB_DATABASE', 'asesco_cobranzas');
$mysqlHost = env('DB_HOST', '127.0.0.1');
$mysqlPort = env('DB_PORT', '3306');
$mysqlUser = env('DB_USERNAME', 'root');
$mysqlPass = env('DB_PASSWORD', '');
$sqlitePath = __DIR__ . '/database.sqlite';

if (!file_exists($sqlitePath)) {
    die("ERROR: No se encontró el archivo SQLite en $sqlitePath\n");
}

echo "=== Migración de SQLite a MySQL ($dbName) ===\n";

// 1. Asegurar base de datos en MySQL
$rootPdo = new PDO("mysql:host=$mysqlHost;port=$mysqlPort", $mysqlUser, $mysqlPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
$rootPdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// 2. Conectar a ambas bases
$sqlite = new PDO("sqlite:$sqlitePath", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$mysql = new PDO("mysql:host=$mysqlHost;port=$mysqlPort;dbname=$dbName", $mysqlUser, $mysqlPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// 3. Desactivar FK
$mysql->exec("SET FOREIGN_KEY_CHECKS = 0;");

$tables = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $exists = $mysql->query("SHOW TABLES LIKE '$table'")->fetchColumn();
    if (!$exists) {
        echo "[SKIP] Tabla $table no existe en MySQL.\n";
        continue;
    }

    $mysql->exec("TRUNCATE TABLE `$table`");

    $rows = $sqlite->query("SELECT * FROM \"$table\"")->fetchAll();
    $count = count($rows);

    if ($count > 0) {
        $columns = array_keys($rows[0]);
        $colNames = implode('`, `', $columns);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $insertStmt = $mysql->prepare("INSERT INTO `$table` (`$colNames`) VALUES ($placeholders)");
        foreach ($rows as $row) {
            $insertStmt->execute(array_values($row));
        }
    }

    $myCount = $mysql->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    echo sprintf("[%s] %-35s SQLite: %4d  ->  MySQL: %4d\n", ($count == $myCount ? 'OK' : 'MISMATCH'), $table, $count, $myCount);
}

$mysql->exec("SET FOREIGN_KEY_CHECKS = 1;");
echo "\n¡Transferencia completada exitosamente!\n";
