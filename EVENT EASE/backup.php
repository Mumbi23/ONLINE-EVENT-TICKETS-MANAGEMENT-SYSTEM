<?php
// backup.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}
require_once __DIR__ . '/db.php';

// Generate file name
$backupFile = 'backup_' . date("Y-m-d_H-i-s") . '.sql';

// Fetch all tables
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$sqlScript = "";
foreach ($tables as $table) {
    $query = $conn->query("SELECT * FROM $table");
    $numColumns = $query->field_count;

    // Drop statement
    $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";

    // Table structure
    $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
    $sqlScript .= "\n" . $row2[1] . ";\n\n";

    // Table data
    for ($i = 0; $i < $numColumns; $i++) {
        while ($row = $query->fetch_row()) {
            $sqlScript .= "INSERT INTO `$table` VALUES(";
            for ($j = 0; $j < $numColumns; $j++) {
                $row[$j] = $row[$j] ? addslashes($row[$j]) : 'NULL';
                $sqlScript .= '"' . $row[$j] . '"';
                if ($j < ($numColumns - 1)) {
                    $sqlScript .= ',';
                }
            }
            $sqlScript .= ");\n";
        }
    }
    $sqlScript .= "\n\n";
}

// Download as .sql
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"$backupFile\"");
echo $sqlScript;
exit;
?>
