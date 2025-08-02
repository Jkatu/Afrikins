<?php
// MAXIMUM ERROR REPORTING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CLEAR ANY PREVIOUS OUTPUT
if (ob_get_level() > 0) ob_end_clean();

echo "<h1>Debugging SQLite Connection</h1>";
echo "<pre>";

// DATABASE PATH - ESCAPE BACKSLASHES
$database_file = 'E:\\SHINA app\\classdb.db';
echo "1. Database path: " . $database_file . "\n\n";

// CHECK FILE EXISTS
echo "2. File exists check: ";
if (file_exists($database_file)) {
    echo "YES\n";
    echo "   File size: " . filesize($database_file) . " bytes\n";
} else {
    die("NO - File not found!");
}

// CHECK READABLE
echo "3. File readable check: ";
if (is_readable($database_file)) {
    echo "YES\n";
} else {
    die("NO - Check permissions!");
}

// TRY CONNECTION
echo "\n4. Attempting connection...\n";
try {
    $db = new SQLite3($database_file);
    echo "   Connection successful!\n";
    
    // TEST BASIC QUERY
    echo "5. SQLite version: " . $db->querySingle('SELECT sqlite_version()') . "\n";
    
    // CHECK TABLES
    echo "\n6. Checking for tables:\n";
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    
    $hasTables = false;
    while ($table = $tables->fetchArray(SQLITE3_ASSOC)) {
        echo "   - Found table: " . $table['name'] . "\n";
        $hasTables = true;
    }
    
    if (!$hasTables) {
        echo "   No tables found in database\n";
    }
    
} catch (Exception $e) {
    die("\nERROR: " . $e->getMessage());
} finally {
    if (isset($db)) {
        $db->close();
    }
}

echo "\nSCRIPT COMPLETED SUCCESSFULLY";
echo "</pre>";
?>