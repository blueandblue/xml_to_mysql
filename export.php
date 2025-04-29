<?php
include "db.php";

if (isset($_POST["exportType"])) {
    $type = $_POST["exportType"];
    $table = $_POST["table"];
    $output = "";

    if ($type == "csv") {
        $filename = "export/$table.csv";
        $query = "SELECT * FROM `$table`";
        $result = $conn->query($query);

        $f = fopen($filename, "w");
        $fields = array_keys($result->fetch_assoc());
        fputcsv($f, $fields);

        while ($row = $result->fetch_assoc()) {
            fputcsv($f, $row);
        }
        fclose($f);

        echo "Esportato in CSV: <a href='$filename' download>Scarica</a>";
    } elseif ($type == "sql") {
        $filename = "export/$table.sql";
        $query = "SHOW CREATE TABLE `$table`";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $output .= $row["Create Table"] . ";\n\n";

        $query = "SELECT * FROM `$table`";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $output .= "INSERT INTO `$table` VALUES (" . implode(",", array_map(fn($v) => "'$v'", $row)) . ");\n";
        }

        file_put_contents($filename, $output);
        echo "Esportato in SQL: <a href='$filename' download>Scarica</a>";
    }
}
?>
