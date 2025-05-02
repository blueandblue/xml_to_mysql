        <!-- Bootstrap 4 -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>

        <!-- Bootstrap Table -->
        <link rel="stylesheet" href="css/bootstrap-table.min.css">
        <script src="js/bootstrap-table.min.js"></script>

<?php
include "db.php";

// Funzione per determinare il tipo di dati MySQL in base al contenuto
function detectDataType($value) {
    if (is_numeric($value)) {
        return strpos($value, '.') !== false ? "FLOAT" : "INT";
    }
    if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $value)) {
        return "DATE";
    }
    if (preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", $value)) {
        return "DATETIME";
    }
    if (strlen($value) > 255) {
        return "TEXT";
    }
    return "VARCHAR(255)";
}

if (isset($_POST["file"]) && isset($_POST["tables"])) {
    $filePath = $_POST["file"];
    $selectedTables = $_POST["tables"];
    $xml = simplexml_load_file($filePath);

    foreach ($xml->children() as $table) {
        $tableName = $table->getName();

        if (!in_array($tableName, $selectedTables)) {
            continue;
        }

        $columns = [];
        $allFieldNames = [];

        // Prima scansione per ottenere tutti i nomi dei campi
        foreach ($table->children() as $row) {
            foreach ($row->children() as $column) {
                $colName = $column->getName();
                $allFieldNames[$colName] = true;
            }
        }

        // Forziamo l'ordine delle colonne
        $orderedFields = array_keys($allFieldNames);
        $orderedFields[] = "data_aggiornamento";

        // Seconda scansione per raccogliere i valori
        $values = [];
        foreach ($table->children() as $row) {
            $rowValues = [];

            foreach ($orderedFields as $colName) {
                if ($colName === "data_aggiornamento") {
                    $rowValues[] = "'" . date("Y-m-d H:i:s") . "'";
                    continue;
                }

                $element = $row->$colName;
                $val = isset($element) ? (string)$element : "";
                $columns[$colName] = detectDataType($val);
                $rowValues[] = "'" . addslashes($val) . "'";
            }

            $values[] = "(" . implode(",", $rowValues) . ")";
        }

        // Aggiunge campo data_aggiornamento
        $columns["data_aggiornamento"] = "DATETIME DEFAULT CURRENT_TIMESTAMP";

        // DROP table
        $conn->query("DROP TABLE IF EXISTS `$tableName`");

        // Crea la tabella
        $columnsSQL = [];
        foreach ($orderedFields as $field) {
            $type = isset($columns[$field]) ? $columns[$field] : "VARCHAR(255)";
            $columnsSQL[] = "`$field` $type";
        }

       // $createQuery = "CREATE TABLE `$tableName` (" . implode(",", $columnsSQL) . ")";
        
        $createQuery = "CREATE TABLE `$tableName` (" . implode(",", $columnsSQL) . ") CHARACTER SET utf8 COLLATE utf8_general_ci";

        $conn->query($createQuery);

        // Inserisci i dati
        if (!empty($values)) {
            $insertQuery = "INSERT INTO `$tableName` (`" . implode("`,`", $orderedFields) . "`) VALUES " . implode(",", $values);
            $conn->query($insertQuery);
        }

        echo "<p class='text-success'>Tabella <b>$tableName</b> importata con successo con il campo <b>data_aggiornamento</b>!</p>";
    }

    // Mostra tutte le tabelle importate
    echo "<h3 class='mt-4'>Tabelle Importate</h3>";
    echo "<table class='table table-bordered' data-toggle='table' data-search='true' data-pagination='true'>";
    echo "<thead><tr><th>Nome Tabella</th><th>Azioni</th></tr></thead><tbody>";

    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tableName = $row[0];
        echo "<tr>";
        echo "<td>$tableName</td>";
        echo "<td>
            <button class='btn btn-info' onclick='viewTable(\"$tableName\")'>Visualizza</button>
            <a href='export_csv.php?table=$tableName' class='btn btn-success'>Esporta CSV</a>
            <a href='export_sql.php?table=$tableName' class='btn btn-warning'>Esporta SQL</a>
        </td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
}
?>

<script>
function viewTable(tableName) {
    window.open('view_table.php?table=' + tableName, '_blank');
}
</script>

