<?php
session_start();
require_once '../../config/db.php'; // Ajusta esta ruta según tu estructura

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados por el formulario
    $id = $_POST['userId'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    // Verificar que los datos estén presentes
    if ($id && $nombre && $apellido && $correo && $rol) {
        try {
            // Preparar la consulta SQL para actualizar los datos
            $query = $pdo->prepare("UPDATE USUARIO SET NOMBRE = :nombre, APELLIDO = :apellido, CORREO = :correo, ROL = :rol WHERE ID = :id");
            $query->execute([
                'id' => $id,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'rol' => $rol
            ]);

            // Verificar si se actualizó algún registro
            if ($query->rowCount() > 0) {
                echo json_encode(['message' => 'Usuario actualizado exitosamente']);
            } else {
                echo json_encode(['message' => 'No se realizaron cambios en los datos']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Faltan datos para actualizar']);
    }
}
?>
