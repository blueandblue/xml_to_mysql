<?php
$host = "localhost";
$user = "INSERIRE USER";
$password = "INSERIRE PASSWORD";
$dbname = "INSERIRE NOME DB";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Imposta charset utf8 (compatibile con vecchi server)
$conn->set_charset("utf8");
?>
