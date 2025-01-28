<?php
// Incluir la conexión a la base de datos
require_once '../../config/db.php';

// Verificar si se recibió un ID válido por el método GET
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Consultar si el registro existe antes de eliminarlo
        $sqlSelect = "SELECT FOTO FROM CANCHA WHERE ID = :id";
        $stmtSelect = $pdo->prepare($sqlSelect);
        $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtSelect->execute();
        $cancha = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($cancha) {
            // Eliminar la foto asociada si existe
            if (!empty($cancha['FOTO']) && file_exists(__DIR__ . '/' . $cancha['FOTO'])) {
                unlink(__DIR__ . '/' . $cancha['FOTO']);
            }

            // Eliminar el registro de la base de datos
            $sqlDelete = "DELETE FROM CANCHA WHERE ID = :id";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Redirigir con éxito
            header("Location: list.php?success=1");
            exit;
        } else {
            echo "<div class='alert alert-danger'>No se encontró la cancha con el ID especificado.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID no válido.</div>";
}
?>
