<?php
include "db.php";

if (!isset($_GET['table']) || empty($_GET['table'])) {
    die("Errore: Nome della tabella non specificato.");
}

$tableName = $conn->real_escape_string($_GET['table']); // Protezione contro SQL Injection
$filename = "{$tableName}_" . date("Y-m-d") . ".csv";

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen("php://output", "w");

// Recupera l'intestazione
$result = $conn->query("SELECT * FROM `$tableName` LIMIT 1");
if ($result->num_rows > 0) {
    $columns = [];
    while ($field = $result->fetch_field()) {
        $columns[] = $field->name;
    }
    fputcsv($output, $columns); // Scrive l'intestazione nel file CSV
}

// Recupera e scrive i dati
$result = $conn->query("SELECT * FROM `$tableName`");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
