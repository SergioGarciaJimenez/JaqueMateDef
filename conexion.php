<?php 
// Uso una bbdd local
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "JaqueMate";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);
// Verificar la conexión
if ($conn->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
} 