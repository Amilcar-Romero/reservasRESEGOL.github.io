<?php
session_start();
require_once '../../config/db.php';

// Verificar que el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    die('Error: Usuario no autenticado');
}

// Obtener el nombre de usuario desde la sesión
$nombreUsuario = $_SESSION['usuario'];

// Consultar el ID de usuario desde la base de datos
$sql = "SELECT ID FROM usuario WHERE NOMBRE = :nombreUsuario LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nombreUsuario', $nombreUsuario);
$stmt->execute();

// Verificar si el usuario existe
if ($stmt->rowCount() == 0) {
    die('Error: Usuario no encontrado');
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);
$idUsuario = $user['ID']; // Obtener el ID del usuario

// Obtener los datos de la reserva
$idCancha = $_POST['cancha_id']; // ID de la cancha seleccionada
$fechaReserva = $_POST['fecha_reserva'];
$horaInicio = $_POST['hora_inicio'];
$horaFin = $_POST['hora_fin'];


// Obtener el ID de la empresa asociada a la cancha seleccionada
$sqlCancha = "SELECT IDEMPRESA FROM CANCHA WHERE ID = :idCancha";
$stmtCancha = $pdo->prepare($sqlCancha);
$stmtCancha->bindParam(':idCancha', $idCancha);
$stmtCancha->execute();
$cancha = $stmtCancha->fetch(PDO::FETCH_ASSOC);

if (!$cancha) {
    die('Error: Cancha no encontrada');
}

$idEmpresa = $cancha['IDEMPRESA']; // Obtener el ID de la empresa

// Asignar un estado por defecto (por ejemplo, 'pendiente')
$estado = 1; // Aquí puedes definir otro estado si es necesario

// Insertar la reserva en la tabla RESERVA
$sqlInsert = "INSERT INTO reserva (IDUSUARIO, IDCANCHA, IDEMPRESA, IDESTADO, FECHARESERVA, HORAINICIO, HORAFIN) 
              VALUES (:idUsuario, :idCancha, :idEmpresa, :estado, :fechaReserva, :horaInicio, :horaFin)";
$stmtInsert = $pdo->prepare($sqlInsert);

// Asignar los parámetros para la consulta
$stmtInsert->bindParam(':idUsuario', $idUsuario);
$stmtInsert->bindParam(':idCancha', $idCancha);
$stmtInsert->bindParam(':idEmpresa', $idEmpresa);
$stmtInsert->bindParam(':estado', $estado);
$stmtInsert->bindParam(':fechaReserva', $fechaReserva);
$stmtInsert->bindParam(':horaInicio', $horaInicio);
$stmtInsert->bindParam(':horaFin', $horaFin);


// Ejecutar la consulta de inserción
// Ejecutar la consulta de inserción
if ($stmtInsert->execute()) {
    // Redirigir con un mensaje de éxito
    header("Location: reservas.php?id_empresa=$idEmpresa&mensaje=Reserva realizada con éxito");
    exit();
} else {
    echo 'Error al realizar la reserva';
}

?>
