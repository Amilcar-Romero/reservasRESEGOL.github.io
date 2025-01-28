<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empresa</title>
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
            height: 600px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <form class="form" action="" method="post" enctype="multipart/form-data">
            <p class="title">Editar Empresa</p>
            <?php
            require_once '../../config/db.php'; // Incluye la conexión a la base de datos

            $id = $_GET['id'] ?? 0; // Obtén el ID de la URL
            $sql = "SELECT * FROM EMPRESA WHERE ID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$empresa) {
                echo "<div class='alert alert-danger'>Empresa no encontrada.</div>";
                exit;
            }
            ?>

            <label>
                <input required type="text" class="input" name="nombre" value="<?= htmlspecialchars($empresa['NOMBRE']) ?>">
                <span>Nombre de la Empresa</span>
            </label>

            <label>
                <input required type="text" class="input" name="direccion" value="<?= htmlspecialchars($empresa['DIRECCION']) ?>">
                <span>Dirección</span>
            </label>

            <label>
                <input required type="text" class="input" name="telefono" value="<?= htmlspecialchars($empresa['TELEFONO']) ?>">
                <span>Teléfono</span>
            </label>

            <div id="mapa"></div>

            <label>
                <input required type="text" id="latitud" class="input" name="latitud" value="<?= round($empresa['LATITUD'], 6) ?>">
                <span>Latitud</span>
            </label>

            <label>
                <input required type="text" id="longitud" class="input" name="longitud" value="<?= round($empresa['LONGITUD'], 6) ?>">
                <span>Longitud</span>
            </label>

            <button id="cargarcoordenadas" class="submit" type="button">Cargar coordenadas</button>

            <label>
                <input type="file" class="input" name="logo">
                <span>Logo (dejar en blanco para no cambiar)</span>
            </label>

            <button class="submit" type="submit" name="submit">Actualizar Empresa</button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $latitud = round($_POST['latitud'], 6);
        $longitud = round($_POST['longitud'], 6);
        $logo = $empresa['LOGO'];

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
            $sql = "UPDATE EMPRESA SET NOMBRE = :nombre, DIRECCION = :direccion, TELEFONO = :telefono, LATITUD = :latitud, LONGITUD = :longitud, LOGO = :logo WHERE ID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':latitud', $latitud);
            $stmt->bindParam(':longitud', $longitud);
            $stmt->bindParam(':logo', $logo);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Redirigir a list.php después de la actualización
            header("Location: list.php");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
    ?>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
        const map = L.map('mapa').setView([<?= round($empresa['LATITUD'], 6) ?>, <?= round($empresa['LONGITUD'], 6) ?>], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        const marker = L.marker([<?= round($empresa['LATITUD'], 6) ?>, <?= round($empresa['LONGITUD'], 6) ?>], {draggable: true}).addTo(map);

        marker.on('dragend', function () {
            const latLng = marker.getLatLng();
            document.getElementById('latitud').value = latLng.lat.toFixed(6);
            document.getElementById('longitud').value = latLng.lng.toFixed(6);
        });

        document.getElementById('cargarcoordenadas').addEventListener('click', function () {
            const latitud = parseFloat(document.getElementById('latitud').value);
            const longitud = parseFloat(document.getElementById('longitud').value);

            if (!isNaN(latitud) && !isNaN(longitud)) {
                const nuevaCoordenada = [latitud, longitud];
                map.setView(nuevaCoordenada, 12);
                marker.setLatLng(nuevaCoordenada);
            } else {
                alert('Por favor, ingresa coordenadas válidas.');
            }
        });
    </script>
</body>
</html>
