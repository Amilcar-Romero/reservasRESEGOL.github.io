<?php
// Incluir la conexión a la base de datos
require_once '../../config/db.php';

// Obtener la ID de la cancha desde la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si no se recibe una ID válida, redirigir al listado
if ($id <= 0) {
    header("Location: ../index.php");
    exit;
}

// Obtener los datos de la cancha
$sql = "SELECT * FROM CANCHA WHERE ID = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$cancha = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra la cancha, redirigir al listado
if (!$cancha) {
    header("Location: ../index.php");
    exit;
}

// Obtener las empresas para el formulario
$sqlEmpresas = "SELECT * FROM EMPRESA";
$stmtEmpresas = $pdo->prepare($sqlEmpresas);
$stmtEmpresas->execute();
$empresas = $stmtEmpresas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cancha</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Modal de Editar Cancha -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-6 text-center">Editar Cancha</h2>
            <form method="post" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data" class="space-y-4">
                <!-- Selección de Empresa -->
                <label for="IDEMPRESA" class="block mb-2">Empresa:</label>
                <select id="IDEMPRESA" name="IDEMPRESA" class="mb-4 p-2 border border-gray-300 rounded w-full" required>
                    <option value="">Seleccione una empresa</option>
                    <?php
                    foreach ($empresas as $empresa) {
                        $selected = $empresa['ID'] == $cancha['IDEMPRESA'] ? 'selected' : '';
                        echo "<option value='" . $empresa['ID'] . "' $selected>" . $empresa['NOMBRE'] . "</option>";
                    }
                    ?>
                </select>

                <!-- Nombre de la Cancha -->
                <div>
                    <label for="NOMBRECANCHA" class="block text-sm font-semibold text-gray-700">Nombre de la Cancha:</label>
                    <input type="text" id="NOMBRECANCHA" name="NOMBRECANCHA" value="<?= htmlspecialchars($cancha['NOMBRECANCHA']) ?>" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <!-- Foto -->
                <div>
                    <label for="FOTO" class="block text-sm font-semibold text-gray-700">Foto:</label>
                    <input type="file" id="FOTO" name="FOTO" class="w-full px-3 py-2 border rounded-lg">
                    <p class="text-sm text-gray-500">Dejar en blanco para mantener la foto actual.</p>
                </div>

                <!-- Tipo de Cancha -->
                <div>
                    <label for="TIPO" class="block text-sm font-semibold text-gray-700">Tipo:</label>
                    <input type="text" id="TIPO" name="TIPO" value="<?= htmlspecialchars($cancha['TIPO']) ?>" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <!-- Cantidad de Jugadores -->
                <div>
                    <label for="CANTJUGADORES" class="block text-sm font-semibold text-gray-700">Cantidad de Jugadores:</label>
                    <input type="number" id="CANTJUGADORES" name="CANTJUGADORES" value="<?= $cancha['CANTJUGADORES'] ?>" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <!-- Precio por Hora -->
                <div>
                    <label for="PRECIOHORA" class="block text-sm font-semibold text-gray-700">Precio por Hora:</label>
                    <input type="text" id="PRECIOHORA" name="PRECIOHORA" value="<?= htmlspecialchars($cancha['PRECIOHORA']) ?>" required class="w-full px-3 py-2 border rounded-lg">
                </div>

                <!-- Botón para guardar -->
                <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg">
                    Guardar Cambios
                </button>
            </form>

            <!-- Botón para cancelar -->
            <div class="text-center mt-4">
                <button onclick="window.location.href='../index.php';" class="text-red-500 hover:underline">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <?php
    // Procesar el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $idempresa = $_POST['IDEMPRESA'];
        $nombrecancha = $_POST['NOMBRECANCHA'];
        $tipo = $_POST['TIPO'];
        $cantjugadores = $_POST['CANTJUGADORES'];
        $preciohora = $_POST['PRECIOHORA'];
        $foto = $cancha['FOTO']; // Mantener la foto actual por defecto

        // Verificar si se ha subido una nueva foto
        if (!empty($_FILES['FOTO']['name'])) {
            $targetDir = __DIR__ . '/descargasimagenes/';
            $targetFile = $targetDir . basename($_FILES['FOTO']['name']);

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['FOTO']['tmp_name'], $targetFile)) {
                $foto = 'descargasimagenes/' . basename($_FILES['FOTO']['name']);
            } else {
                echo "<div class='alert alert-danger mt-3'>Error al subir el archivo</div>";
            }
        }

        // Actualizar los datos en la base de datos
        try {
            $sql = "UPDATE CANCHA SET IDEMPRESA = :idempresa, NOMBRECANCHA = :nombrecancha, TIPO = :tipo, 
                    CANTJUGADORES = :cantjugadores, PRECIOHORA = :preciohora, FOTO = :foto WHERE ID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idempresa', $idempresa);
            $stmt->bindParam(':nombrecancha', $nombrecancha);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':cantjugadores', $cantjugadores);
            $stmt->bindParam(':preciohora', $preciohora);
            $stmt->bindParam(':foto', $foto);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../index.php");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
        }
    }
    ?>
</body>
</html>
