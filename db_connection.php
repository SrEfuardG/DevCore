<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "devcore_blog";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Opcional: Establecer el juego de caracteres a UTF-8
$conn->set_charset("utf8");
?>