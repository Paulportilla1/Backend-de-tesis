<?php
$host = "localhost";        // o la IP de tu servidor MySQL
$username = "root";         // tu nombre de usuario de MySQL
$password = "";             // tu contraseña de MySQL
$dbname = "tesis";          // el nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
