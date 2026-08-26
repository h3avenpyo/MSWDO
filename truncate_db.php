<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();
    
    echo "Connected to database: {$databaseName}\n";
    echo "Getting all tables except 'users'...\n";
    
    // Get all tables except 'users'
    $tables = $connection->select("SHOW TABLES");
    
    $tablesToTruncate = [];
    foreach ($tables as $table) {
        $tableArray = (array) $table;
        $tableName = reset($tableArray); // Get first value from array
        if ($tableName !== 'users') {
            $tablesToTruncate[] = $tableName;
        }
    }
    
    if (empty($tablesToTruncate)) {
        echo "No tables to truncate (only 'users' table exists).\n";
        exit(0);
    }
    
    echo "Found " . count($tablesToTruncate) . " tables to truncate:\n";
    foreach ($tablesToTruncate as $table) {
        echo "  - {$table}\n";
    }
    
    echo "\nDisabling foreign key checks...\n";
    $connection->statement('SET FOREIGN_KEY_CHECKS=0');
    
    echo "Truncating tables...\n";
    foreach ($tablesToTruncate as $table) {
        try {
            $connection->table($table)->truncate();
            echo "  ✓ Truncated {$table}\n";
        } catch (\Exception $e) {
            echo "  ✗ Failed to truncate {$table}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nRe-enabling foreign key checks...\n";
    $connection->statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\n✓ Database truncation completed successfully!\n";
    echo "The 'users' table was preserved.\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
