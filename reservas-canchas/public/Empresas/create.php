<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            position: relative;
            margin: auto;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .title {
            font-size: 28px;
            color: royalblue;
            font-weight: 600;
            letter-spacing: -1px;
            position: relative;
            display: flex;
            align-items: center;
            padding-left: 30px;
        }

        .form label .input {
            width: 100%;
            padding: 10px;
            outline: 0;
            border: 1px solid rgba(105, 105, 105, 0.397);
            border-radius: 10px;
        }

        .submit {
            border: none;
            outline: none;
            background-color: royalblue;
            padding: 10px;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            transform: .3s ease;
        }

        .submit:hover {
            background-color: rgb(56, 90, 194);
        }

        #mapa {
            width: 100%;
            height: 600px; /* Aumentado de 500px a 600px */
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <form class="form" action="" method="post" enctype="multipart/form-data">
            <p class="title">Agregar Nueva Empresa</p>
            <label>
                <input required type="text" class="input" name="nombre">
                <span>Nombre de la Empresa</span>
            </label>

            <label>
                <input required type="text" class="input" name="direccion">
                <span>Dirección</span>
            </label>

            <label>
                <input required type="text" class="input" name="telefono">
                <span>Teléfono</span>
            </label>

            <div id="mapa"></div>

            <label>
                <input required type="text" id="latitud" class="input" name="latitud">
                <span>Latitud</span>
            </label>

            <label>
                <input required type="text" id="longitud" class="input" name="longitud">
                <span>Longitud</span>
            </label>

            <button id="cargarcoordenadas" class="submit" type="button">Cargar coordenadas</button>

            <label>
                <input type="file" class="input" name="logo">
                <span>Logo</span>
            </label>

            <button class="submit" type="submit" name="submit">Agregar Empresa</button>

            <p class="signin">¿Ya tienes cuenta? <a href="list.php">Ver Empresas</a></p>
        </form>
    </div>

    <?php
    // Procesamiento de formulario (mantener el bloque PHP original)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once '../../config/db.php'; // Incluye la conexión a la base de datos

        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $latitud = round($_POST['latitud'], 6);
        $longitud = round($_POST['longitud'], 6);
        $logo = '';

        if (!empty($_FILES['logo']['name'])) {
            $targetDir = __DIR__ . '/descargasimagenes/';
            $targetFile = $targetDir . basename($_FILES['logo']['name']);

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
                $logo = 'descargasimagenes/' . basename($_FILES['logo']['name']);
            } else {
                echo "<div class='alert alert-danger mt-3'>Error al subir el archivo</div>";
            }
        }

        try {
            $sql = "INSERT INTO EMPRESA (NOMBRE, DIRECCION, TELEFONO, LATITUD, LONGITUD, LOGO) VALUES (:nombre, :direccion, :telefono, :latitud, :longitud, :logo)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':latitud', $latitud);
            $stmt->bindParam(':longitud', $longitud);
            $stmt->bindParam(':logo', $logo);
            $stmt->execute();

            header("Location: list.php");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
        }
    }
    ?>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
        var mapa = L.map('mapa').setView([-17.778213, -63.150070], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(mapa);

        var marcador = L.marker([-17.778213, -63.150070], {draggable: true}).addTo(mapa);

        marcador.on('dragend', function (e) {
            var latLng = marcador.getLatLng();
            document.getElementById('latitud').value = latLng.lat.toFixed(6);
            document.getElementById('longitud').value = latLng.lng.toFixed(6);
        });

        document.getElementById('cargarcoordenadas').addEventListener('click', function () {
            var latitud = parseFloat(document.getElementById('latitud').value);
            var longitud = parseFloat(document.getElementById('longitud').value);

            if (!isNaN(latitud) && !isNaN(longitud)) {
                var nuevaCoordenada = [latitud, longitud];
                mapa.setView(nuevaCoordenada, 12);
                marcador.setLatLng(nuevaCoordenada);
            } else {
                alert('Por favor, ingresa coordenadas válidas.');
            }
        });
    </script>
</body>
</html>
