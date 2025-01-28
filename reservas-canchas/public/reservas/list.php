<?php
session_start();
require_once '../../config/db.php'; // Asegúrate de que la ruta sea correcta

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit();
}

// Obtener el ID del usuario logueado
$consultaUsuario = $pdo->prepare("SELECT ID FROM usuario WHERE NOMBRE = :usuario");
$consultaUsuario->bindParam(':usuario', $_SESSION['usuario'], PDO::PARAM_STR);
$consultaUsuario->execute();
$usuarioData = $consultaUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuarioData) {
    echo "Usuario no encontrado.";
    exit();
}

$usuarioLogueado = $usuarioData['ID'];

// Consultar las reservas del usuario con nombres de cancha, empresa y estado
$query = $pdo->prepare("
    SELECT 
        r.FECHARESERVA, 
        r.HORAINICIO, 
        r.HORAFIN, 
        c.NOMBRECANCHA AS CANCHA, 
        e.NOMBRE AS EMPRESA, 
        es.NOMBRE AS ESTADO
    FROM reserva r
    INNER JOIN cancha c ON r.IDCANCHA = c.ID
    INNER JOIN empresa e ON r.IDEMPRESA = e.ID
    INNER JOIN estado es ON r.IDESTADO = es.ID
    WHERE r.IDUSUARIO = :usuario
");
$query->bindParam(':usuario', $usuarioLogueado, PDO::PARAM_INT);
$query->execute();
$reservas = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5 pt-5">
        <h1>Mis Reservas</h1>
        <a href="../index.php" class="btn btn-primary mb-3">Volver al inicio</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Cancha</th>
                    <th>Empresa</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservas)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No tienes reservas registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservas as $index => $reserva): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($reserva['FECHARESERVA']) ?></td>
                            <td><?= htmlspecialchars($reserva['HORAINICIO']) ?></td>
                            <td><?= htmlspecialchars($reserva['HORAFIN']) ?></td>
                            <td><?= htmlspecialchars($reserva['CANCHA']) ?></td>
                            <td><?= htmlspecialchars($reserva['EMPRESA']) ?></td>
                            <td><?= htmlspecialchars($reserva['ESTADO']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
