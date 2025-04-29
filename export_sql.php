<?php
include "db.php";

if (!isset($_GET['table'])) {
    die("Tabella non specificata.");
}

$table = $conn->real_escape_string($_GET['table']);
$filename = $table . "_" . date("Ymd_His") . ".sql";

// Imposta gli header per il download
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Recupera la struttura della tabella
$createTableResult = $conn->query("SHOW CREATE TABLE `$table`");
if (!$createTableResult) {
    die("Errore nella creazione della tabella SQL.");
}
$createTableRow = $createTableResult->fetch_assoc();
echo $createTableRow['Create Table'] . ";\n\n";

// Recupera i dati dalla tabella
$result = $conn->query("SELECT * FROM `$table`");

while ($row = $result->fetch_assoc()) {
    $columns = array_map(function($col) {
        return "`" . $col . "`";
    }, array_keys($row));

    $values = array_map(function($val) use ($conn) {
        return isset($val) ? "'" . $conn->real_escape_string($val) . "'" : "NULL";
    }, array_values($row));

    echo "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n";
}

exit;
?>

