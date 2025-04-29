<?php
if ($_FILES["xmlFile"]["error"] == UPLOAD_ERR_OK) {
    $uploadDir = "uploads/";
    $filePath = $uploadDir . basename($_FILES["xmlFile"]["name"]);

    if (move_uploaded_file($_FILES["xmlFile"]["tmp_name"], $filePath)) {
        echo "<p class='text-success'>File caricato con successo!</p>";

        if (!file_exists($filePath)) {
            echo "<p class='text-danger'>Errore: il file XML non è stato trovato!</p>";
            exit;
        }

        $xml = simplexml_load_file($filePath);
        if ($xml === false) {
            echo "<p class='text-danger'>Errore: impossibile leggere il file XML.</p>";
            exit;
        }

        echo "<h4>Seleziona le tabelle da importare:</h4>";
        echo "<form id='importForm'>";
        echo "<input type='hidden' name='file' value='$filePath'>";

        $tablesFound = false;

        // Legge tutti i nodi principali del file XML (che rappresentano le tabelle)
        foreach ($xml->children() as $table) {
            $tableName = $table->getName(); // Prende il nome del nodo come nome della tabella
            echo "<div class='form-check'>";
            echo "<input class='form-check-input' type='checkbox' name='tables[]' value='$tableName' id='table_$tableName' checked>";
            echo "<label class='form-check-label' for='table_$tableName'>$tableName</label>";
            echo "</div>";
            $tablesFound = true;
        }

        if (!$tablesFound) {
            echo "<p class='text-danger'>Nessuna tabella trovata nel file XML!</p>";
        } else {
            echo "<button type='button' class='btn btn-success mt-3' onclick='processSelectedTables()'>Importa Selezionate</button>";
        }
        
        echo "</form>";
    } else {
        echo "<p class='text-danger'>Errore nel caricamento del file.</p>";
    }
} else {
    echo "<p class='text-danger'>Errore: nessun file ricevuto.</p>";
}
?>
