<?php
session_start();
require_once '../../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Consultar los datos del usuario por ID
    $query = $pdo->prepare("SELECT u.ID, u.NOMBRE, u.APELLIDO, u.CORREO, u.ROL FROM USUARIO u WHERE u.ID = :id");
    $query->execute(['id' => $id]);
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode($usuario);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
}
?>
