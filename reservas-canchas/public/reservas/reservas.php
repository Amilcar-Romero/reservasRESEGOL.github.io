<?php
session_start();
require_once '../../config/db.php';

// Verifica si se ha pasado el ID de la empresa en la URL
if (!isset($_GET['id_empresa'])) {
    die('Error: ID de la empresa no proporcionado');
}

$id_empresa = $_GET['id_empresa'];

// Obtener el nombre de la empresa
$sql_empresa = "SELECT NOMBRE FROM EMPRESA WHERE ID = :id_empresa";
$stmt_empresa = $pdo->prepare($sql_empresa);
$stmt_empresa->bindParam(':id_empresa', $id_empresa);
$stmt_empresa->execute();

// Obtener el nombre de la empresa
$empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
$nombre_empresa = $empresa['NOMBRE']; // Esto contiene el nombre de la empresa

// Obtener las canchas de la empresa seleccionada
$sql = "SELECT * FROM CANCHA WHERE IDEMPRESA = :id_empresa";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_empresa', $id_empresa);
$stmt->execute();

// Obtener las reservas de la empresa seleccionada
$sql_reservas = "SELECT * FROM RESERVA WHERE IDEMPRESA = :id_empresa";
$stmt_reservas = $pdo->prepare($sql_reservas);
$stmt_reservas->bindParam(':id_empresa', $id_empresa);
$stmt_reservas->execute();

// Mostrar las canchas
$canchas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$reservas = $stmt_reservas->fetchAll(PDO::FETCH_ASSOC);

// Obtener el precio de la cancha seleccionada
if (isset($_POST['cancha_id'])) {
    $idCancha = $_POST['cancha_id']; // ID de la cancha seleccionada
    $sql = "SELECT PRECIOHORA FROM CANCHA WHERE ID = :idCancha";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idCancha', $idCancha);
    $stmt->execute();
    
    $cancha = $stmt->fetch(PDO::FETCH_ASSOC);
    $precioPorHora = $cancha['PRECIOHORA']; // Guardar el precio en una variable
} else {
    // Asignar un valor por defecto si no se ha seleccionado la cancha
    $precioPorHora = 100; // Por ejemplo, un valor por defecto
}
// Obtener todos los estados de la base de datos
$queryEstados = $pdo->prepare("SELECT * FROM ESTADO");
$queryEstados->execute();
$estados = $queryEstados->fetchAll(PDO::FETCH_ASSOC);
// Crear un arreglo para mapear los IDs de estado a los nombres de estado
$estadoMap = [];
foreach ($estados as $estado) {
    $estadoMap[$estado['ID']] = $estado['NOMBRE'];
}
if (isset($_POST['accion'], $_POST['id_reserva'])) {
    $id_reserva = $_POST['id_reserva'];
    $nuevo_estado = ($_POST['accion'] === 'aceptar') ? 2 : 3; // ID 2 para aceptado, ID 3 para cancelado

    $sql_actualizar_estado = "UPDATE RESERVA SET IDESTADO = :nuevo_estado WHERE ID = :id_reserva";
    $stmt_actualizar = $pdo->prepare($sql_actualizar_estado);
    $stmt_actualizar->bindParam(':nuevo_estado', $nuevo_estado);
    $stmt_actualizar->bindParam(':id_reserva', $id_reserva);

    if ($stmt_actualizar->execute()) {
        header("Location: ?id_empresa=$id_empresa&mensaje=Estado actualizado correctamente");
        exit;
    } else {
        echo "Error al actualizar el estado.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
    <?php
        if (isset($_GET['mensaje'])) {
            echo '<div class="alert alert-success" role="alert">';
            echo htmlspecialchars($_GET['mensaje']);
            echo '</div>';
        }
        ?>
        <h2>Cancha de la Empresa Seleccionada</h2>

        <div class="row">
            <?php foreach ($canchas as $cancha): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="../canchas/<?= $cancha['FOTO'] ?>" class="card-img-top" alt="Imagen de la cancha">
                        <div class="card-body">
                            <h5 class="card-title"><?= $cancha['NOMBRECANCHA'] ?></h5>
                            <p class="card-text">
                                Tipo: <?= $cancha['TIPO'] ?><br>
                                Jugadores: <?= $cancha['CANTJUGADORES'] ?><br>
                                Precio por hora: <?= number_format($cancha['PRECIOHORA'], 2) ?> USD
                            </p>
                            <!-- Botón para abrir el modal -->
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reservaModal" onclick="setCanchaInfo('<?= $cancha['NOMBRECANCHA'] ?>', '<?= $cancha['ID'] ?>')">Reservar</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal -->
<div class="modal fade" id="reservaModal" tabindex="-1" aria-labelledby="reservaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservaModalLabel">Registrar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="create.php" method="POST">
                    <input type="hidden" id="id_empresa" name="id_empresa"> <!-- Aquí pasamos el IDEMPRESA -->
                    <input type="hidden" id="cancha_id" name="cancha_id"> <!-- Aquí pasamos el ID de la cancha -->
                    
                    <!-- Otros campos del formulario -->
                    <div class="mb-3">
                        <label for="nombre_empresa" class="form-label">Empresa</label>
                        <input type="text" class="form-control" id="nombre_empresa" name="empresa" value="<?= $nombre_empresa ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_cancha" class="form-label">Cancha</label>
                        <input type="text" class="form-control" id="nombre_cancha" name="cancha" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_usuario" class="form-label">Tu Nombre</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="usuario" value="<?= $_SESSION['usuario'] ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_reserva" class="form-label">Fecha de Reserva</label>
                        <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                        <select class="form-control" id="hora_inicio" name="hora_inicio" required>
                            <!-- Las opciones se llenarán con JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hora_fin" class="form-label">Hora de Fin</label>
                        <select class="form-control" id="hora_fin" name="hora_fin" required>
                            <!-- Las opciones se llenarán con JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="total" class="form-label">Total</label>
                        <input type="text" class="form-control" id="total" name="total" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Reserva</button>
                </form>
            </div>
        </div>
    </div>
</div>


        <h3 class="mt-5">Reservas de la Empresa</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Reserva</th>
                    <th>Fecha de Reserva</th>
                    <th>Hora de Inicio</th>
                    <th>Hora de Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= $reserva['ID'] ?></td>
                        <td><?= $reserva['FECHARESERVA'] ?></td>
                        <td><?= $reserva['HORAINICIO'] ?></td>
                        <td><?= $reserva['HORAFIN'] ?></td>
                        <td><?= isset($estadoMap[$reserva['IDESTADO']]) ? $estadoMap[$reserva['IDESTADO']] : 'Estado desconocido' ?></td>
                        <td>
                            <?php if ($reserva['IDESTADO'] == 1): // Solo mostrar botones si el estado es pendiente ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirmarAccion('aceptar')">
                                    <input type="hidden" name="id_reserva" value="<?= $reserva['ID'] ?>">
                                    <button type="submit" name="accion" value="aceptar" class="btn btn-success btn-sm">Aceptar</button>
                                </form>
                                <form method="POST" style="display: inline;" onsubmit="return confirmarAccion('cancelar')">
                                    <input type="hidden" name="id_reserva" value="<?= $reserva['ID'] ?>">
                                    <button type="submit" name="accion" value="cancelar" class="btn btn-danger btn-sm">Cancelar</button>
                                </form>
                            <?php elseif ($reserva['IDESTADO'] == 2): // Si está aceptada, mostrar solo el botón de cancelar ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirmarAccion('cancelar')">
                                    <input type="hidden" name="id_reserva" value="<?= $reserva['ID'] ?>">
                                    <button type="submit" name="accion" value="cancelar" class="btn btn-danger btn-sm">Cancelar</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Sin acciones</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmarAccion(accion) {
    return confirm("¿Desea confirmar esta acción de " + (accion === "aceptar" ? "aceptar" : "cancelar") + " la reserva?");
}

        // Función para poner la información de la cancha seleccionada en el modal
        // Función para poner la información de la cancha seleccionada en el modal
// Función para poner la información de la cancha seleccionada en el modal
function setCanchaInfo(nombreCancha, idCancha, idEmpresa) {
    document.getElementById('nombre_cancha').value = nombreCancha;
    document.getElementById('cancha_id').value = idCancha;
    document.getElementById('id_empresa').value = idEmpresa;
}



        // Pre-cargar las horas con intervalo de 30 minutos
        function cargarHoras() {
            let horaInicio = document.getElementById('hora_inicio');
            let horaFin = document.getElementById('hora_fin');

            let horas = [];
            for (let h = 8; h < 24; h++) {
                for (let m = 0; m < 60; m += 30) {
                    let hora = h.toString().padStart(2, '0') + ':' + m.toString().padStart(2, '0');
                    horas.push(hora);
                }
            }

            // Llenar las opciones de hora de inicio y fin
            horas.forEach(hora => {
                let optionInicio = document.createElement("option");
                optionInicio.value = hora;
                optionInicio.innerHTML = hora;
                horaInicio.appendChild(optionInicio);

                let optionFin = document.createElement("option");
                optionFin.value = hora;
                optionFin.innerHTML = hora;
                horaFin.appendChild(optionFin);
            });

            // Establecer los valores vacíos al cargar la página
            horaInicio.value = '';
            horaFin.value = '';
        }

        // Calcular el total basado en la hora de inicio y fin
        function calcularTotal() {
            let horaInicio = document.getElementById('hora_inicio').value;
            let horaFin = document.getElementById('hora_fin').value;

            // Solo hacer el cálculo si ambas horas están seleccionadas
            if (horaInicio && horaFin) {
                let [hInicio, mInicio] = horaInicio.split(":").map(Number);
                let [hFin, mFin] = horaFin.split(":").map(Number);

                let minutosInicio = hInicio * 60 + mInicio;
                let minutosFin = hFin * 60 + mFin;

                // Asegurarse de que la hora de fin sea mayor que la de inicio
                if (minutosFin <= minutosInicio) {
                    alert("La hora de fin debe ser mayor que la hora de inicio.");
                    document.getElementById('total').value = ''; // Limpiar el total
                    return;
                }

                let diferenciaMinutos = minutosFin - minutosInicio;
                let horasTotales = diferenciaMinutos / 60;

                // Calcular el total, multiplicando las horas totales por el precio por hora
                let precioPorHora = <?= $precioPorHora ?>; 
                let total = precioPorHora * horasTotales;
                
                // Mostrar el total con dos decimales
                document.getElementById('total').value = total.toFixed(2);
            } else {
                document.getElementById('total').value = '';
            }
        }

        // Bloquear horas anteriores a la hora de inicio en el campo hora_fin
        function bloquearHoras() {
            let horaInicio = document.getElementById('hora_inicio').value;
            let horaFin = document.getElementById('hora_fin');
            let horas = Array.from(horaFin.options);

            if (horaInicio) {
                let [hInicio, mInicio] = horaInicio.split(":").map(Number);
                let minutosInicio = hInicio * 60 + mInicio;

                // Habilitar solo las horas posteriores a la hora de inicio
                horas.forEach(option => {
                    let [hFin, mFin] = option.value.split(":").map(Number);
                    let minutosFin = hFin * 60 + mFin;

                    if (minutosFin <= minutosInicio) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            }
        }

        // Validar la selección de la fecha y hora
        document.getElementById('fecha_reserva').addEventListener('change', () => {
            calcularTotal();
        });
        document.getElementById('hora_inicio').addEventListener('change', () => {
            calcularTotal();
            bloquearHoras(); // Bloquear horas en hora_fin
        });
        document.getElementById('hora_fin').addEventListener('change', () => {
            calcularTotal();
        });

        // Cargar las horas cuando la página esté lista
        window.onload = function() {
            cargarHoras();
            document.getElementById('total').value = ''; // Limpiar el total al inicio
        };

    </script>
</body>
</html>