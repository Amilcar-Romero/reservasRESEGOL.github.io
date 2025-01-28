<?php
session_start();
require_once '../../config/db.php'; // Ajusta esta ruta según tu estructura



if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Eliminar el usuario de la base de datos
    $query = $pdo->prepare("DELETE FROM USUARIO WHERE ID = :id");
    $query->execute(['id' => $id]);

    // Redirigir al administrador de usuarios después de eliminar
    header('Location: index.php');
    exit();
} else {
    // Si no se proporciona un ID, redirige de vuelta
    header('Location: index.php');
    exit();
}
?>
