        <!-- Bootstrap 4 -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>

        <!-- Bootstrap Table -->
        <link rel="stylesheet" href="css/bootstrap-table.min.css">
        <script src="js/bootstrap-table.min.js"></script>
<?php
include "db.php";

if (isset($_GET['table'])) {
    $tableName = $_GET['table'];
        echo '<div class="container-md border">';
        echo "<h3>Contenuto della tabella: $tableName</h3>";
        echo "<table class='table table-striped table-bordered' data-toggle='table' data-search='true' data-pagination='true' data-height='560'>";
    
        $result = $conn->query("SELECT * FROM `$tableName`");
    
        if ($result->num_rows > 0) {
            echo "<thead><tr>";
            while ($field = $result->fetch_field()) {
                echo "<th>{$field->name}</th>";
            }
            echo "</tr></thead><tbody>";
        
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
        
            echo "</tbody>";
        } else {
            echo "<tr><td colspan='100%'>Nessun dato trovato</td></tr>";
        }
    
        echo "</table>";
}
?>
