<?php

$local = [
    'host' => '127.0.0.1',
    'db' => 'db_refilling',
    'user' => 'root',
    'pass' => 'Earl2617020200310'
];

$cloud = [
    'host' => 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com',
    'db' => 'db_refilling',
    'user' => '48MJ89GNWcvjb7u.root',
    'pass' => '7p0nBZYamcIWP4p9',
    'ca' => 'isrgrootx1.pem'
];

$tables = [
    'users',
    'tbl_ship_deliveries',
    'tbl_inventory_containers',
    'tbl_backwash_logs',
    'tbl_production',
    'tbl_backwash_status',
    'tbl_expenses',
    'tbl_notifications',
    'tbl_sales_walkin'
];

try {
    $localPdo = new PDO("mysql:host={$local['host']};dbname={$local['db']}", $local['user'], $local['pass']);
    $localPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $cloudPdo = new PDO("mysql:host={$cloud['host']};port=4000;dbname={$cloud['db']}", $cloud['user'], $cloud['pass'], [
        PDO::MYSQL_ATTR_SSL_CA => $cloud['ca'],
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Connections established. Starting data sync...\n";

    // Disable foreign key checks for clean wipe/sync
    $cloudPdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    foreach ($tables as $table) {
        echo "Syncing table: $table...\n";
        
        // Clear cloud table
        $cloudPdo->exec("TRUNCATE TABLE $table");

        // Fetch from local
        $stmt = $localPdo->query("SELECT * FROM $table");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            $cols = array_keys($rows[0]);
            $colNames = implode(',', $cols);
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            
            $insertStmt = $cloudPdo->prepare("INSERT INTO $table ($colNames) VALUES ($placeholders)");
            
            foreach ($rows as $row) {
                // Ensure password and other sensitive fields are synced correctly
                $insertStmt->execute(array_values($row));
            }
            echo " - Synced " . count($rows) . " rows.\n";
        } else {
            echo " - Table empty.\n";
        }
    }

    $cloudPdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "\nSUCCESS: Data synchronization complete! 🚀\n";

} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}
