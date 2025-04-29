<?php
$host = "localhost";
$user = "root";
$password = "mmtmmt";
$dbname = "appoggio";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Imposta charset utf8 (compatibile con vecchi server)
$conn->set_charset("utf8");
?>
