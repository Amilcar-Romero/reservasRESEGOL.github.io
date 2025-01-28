<?php
session_start();
require_once '../config/db.php'; // Asegúrate de tener esta ruta para conectarte a la base de datos

// Consulta para obtener las empresas con sus coordenadas
$sql = "SELECT * FROM EMPRESA";
$stmt = $pdo->query($sql);

// Crear un array para almacenar las ubicaciones
$locations = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Solo agregar empresas con latitud y longitud
    if (!empty($row['LATITUD']) && !empty($row['LONGITUD'])) {
        $locations[] = [
            'NOMBRE' => $row['NOMBRE'],
            'LATITUD' => $row['LATITUD'],
            'LONGITUD' => $row['LONGITUD'],
            'IDEMPRESA' => $row['ID'] // Agregar el ID de la empresa
        ];
    }
}

// Pasar las ubicaciones a JavaScript
$locationsJson = json_encode($locations);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" /> <!-- Leaflet CSS -->
</head>
<style>
    /* Hacer que el mapa ocupe toda la pantalla */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    #map {
        width: 100%;
        height: 100%;
    }

    /* Estilo para el texto superior */
    #info-text {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 15px;
        border-radius: 5px;
        font-size: 16px;
        display: none;
    }
</style>
<body>
    <?php include_once '../includes/header.php'; ?>
    <main class="container my-5">
    </main>
    <div id="map"></div>


    <?php include_once '../includes/footer.php'; ?>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
    // Inicializa el mapa
    var map = L.map('map').setView([-17.783327, -63.182140], 14); // Coordenadas iniciales de Santa Cruz, Bolivia

    // Añade el mapa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Obtener las ubicaciones de las empresas desde PHP
    var locations = <?php echo $locationsJson; ?>;

    // Variable para verificar si el usuario está logueado
    var isLoggedIn = <?php echo json_encode(isset($_SESSION['usuario'])); ?>;

    // Añadir un marcador para cada empresa
    locations.forEach(function(location) {
        const marker = L.marker([location.LATITUD, location.LONGITUD])
            .addTo(map)
            .bindPopup(`<b>${location.NOMBRE}</b>`);

        // Evento de clic en el marcador para mostrar el nombre arriba
        marker.on('click', function() {
            // Muestra el nombre de la empresa en la parte superior
            document.getElementById('info-text').style.display = 'block';
            document.getElementById('info-text').innerHTML = 'Empresa: ' + location.NOMBRE + '<br>Presione 2 veces para ir a reserva';

            // Desaparece después de 3 segundos
            setTimeout(function() {
                document.getElementById('info-text').style.display = 'none';
            }, 3000);
        });

        // Evento de doble clic para redirigir a la página de reservas o login
        marker.on('dblclick', function() {
            if (isLoggedIn) {
                // Si el usuario está logueado, redirige a la página de reservas
                window.location.href = 'reservas/reservas.php?id_empresa=' + location.IDEMPRESA;
            } else {
                // Si no está logueado, redirige al login
                window.location.href = 'login.php';
            }
        });
    });
</script>

</body>
</html>
