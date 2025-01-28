<?php
require_once '../../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener la información de la empresa
    $stmt = $pdo->prepare("SELECT LOGO FROM EMPRESA WHERE ID = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $empresa = $stmt->fetch();

    if ($empresa) {
        // Eliminar la imagen del servidor
        if (!empty($empresa['LOGO']) && file_exists('../../' . $empresa['LOGO'])) {
            unlink('../../' . $empresa['LOGO']);
        }

        // Eliminar la empresa de la base de datos
        $stmt = $pdo->prepare("DELETE FROM EMPRESA WHERE ID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Redirigir a la página de listado
        header("Location: list.php");
        exit;
    } else {
        echo "Empresa no encontrada.";
    }
} else {
    echo "ID no proporcionado.";
}
?>
