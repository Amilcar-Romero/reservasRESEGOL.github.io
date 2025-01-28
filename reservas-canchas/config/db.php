<?php
// Archivo: config/db.php

$host = 'localhost';
$dbname = 'canchabd'; // Asegúrate de que el nombre de la base de datos sea correcto
$username = 'root';
$password = ''; // Deja vacío si no usas contraseña en tu MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

