<?php
// Incluir la conexión a la base de datos
require_once '../../config/db.php';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
    $idempresa = $_POST['IDEMPRESA'];
    $nombrecancha = $_POST['NOMBRECANCHA'];
    $tipo = $_POST['TIPO'];
    $cantjugadores = $_POST['CANTJUGADORES'];
    $preciohora = $_POST['PRECIOHORA'];
    $foto = '';

    // Verificar si se ha subido una foto
    if (!empty($_FILES['FOTO']['name'])) {
        $targetDir = __DIR__ . '/descargasimagenes/'; // Ruta absoluta a la carpeta de imágenes
        $targetFile = $targetDir . basename($_FILES['FOTO']['name']);

        // Verificar si la carpeta existe, si no la crea
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Mover la imagen al servidor
        if (move_uploaded_file($_FILES['FOTO']['tmp_name'], $targetFile)) {
            $foto = 'descargasimagenes/' . basename($_FILES['FOTO']['name']);
        } else {
            echo "<div class='alert alert-danger mt-3'>Error al subir el archivo</div>";
        }
    }

    // Insertar la cancha en la base de datos
    try {
        $sql = "INSERT INTO CANCHA (IDEMPRESA, NOMBRECANCHA, TIPO, CANTJUGADORES, PRECIOHORA, FOTO) 
                VALUES (:idempresa, :nombrecancha, :tipo, :cantjugadores, :preciohora, :foto)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idempresa', $idempresa);
        $stmt->bindParam(':nombrecancha', $nombrecancha);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':cantjugadores', $cantjugadores);
        $stmt->bindParam(':preciohora', $preciohora);
        $stmt->bindParam(':foto', $foto);
        $stmt->execute();

        // Redirigir a la página de listado
        header("Location: ../canchas/list.php");
        exit;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
    }
}

// Obtener las empresas de la base de datos con PDO
$sql = "SELECT * FROM EMPRESA"; 
$stmt = $pdo->prepare($sql);
$stmt->execute();
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cancha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* From Uiverse.io by Yaya12085 */ 
        .form {
          display: flex;
          flex-direction: column;
          gap: 10px;
          max-width: 350px;
          background-color: #fff;
          padding: 20px;
          border-radius: 20px;
          position: relative;
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

        .title::before,.title::after {
          position: absolute;
          content: "";
          height: 16px;
          width: 16px;
          border-radius: 50%;
          left: 0px;
          background-color: royalblue;
        }

        .title::before {
          width: 18px;
          height: 18px;
          background-color: royalblue;
        }

        .title::after {
          width: 18px;
          height: 18px;
          animation: pulse 1s linear infinite;
        }

        .message, .signin {
          color: rgba(88, 87, 87, 0.822);
          font-size: 14px;
        }

        .signin {
          text-align: center;
        }

        .signin a {
          color: royalblue;
        }

        .signin a:hover {
          text-decoration: underline royalblue;
        }

        .flex {
          display: flex;
          width: 100%;
          gap: 6px;
        }

        .form label {
          position: relative;
        }

        .form label .input {
          width: 100%;
          padding: 10px 10px 20px 10px;
          outline: 0;
          border: 1px solid rgba(105, 105, 105, 0.397);
          border-radius: 10px;
        }

        .form label .input + span {
          position: absolute;
          left: 10px;
          top: 15px;
          color: grey;
          font-size: 0.9em;
          cursor: text;
          transition: 0.3s ease;
        }

        .form label .input:placeholder-shown + span {
          top: 15px;
          font-size: 0.9em;
        }

        .form label .input:focus + span,.form label .input:valid + span {
          top: 30px;
          font-size: 0.7em;
          font-weight: 600;
        }

        .form label .input:valid + span {
          color: green;
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

        @keyframes pulse {
          from {
            transform: scale(0.9);
            opacity: 1;
          }

          to {
            transform: scale(1.8);
            opacity: 0;
          }
        }
    </style>
</head>
<body>
    <!-- Modal de Crear Cancha -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <form method="post" action="create.php" enctype="multipart/form-data" class="form">
                <p class="title">Crear Nueva Cancha</p>
                <p class="message">Complete los campos para crear una nueva cancha.</p>

                <!-- Selección de Empresa -->
                <label>
                    <select id="IDEMPRESA" name="IDEMPRESA" class="input" required>
                        <option value=""></option>
                        <?php
                        // Mostrar las empresas
                        if (!empty($empresas)) {
                            foreach ($empresas as $row) {
                                echo "<option value='" . $row['ID'] . "'>" . $row['NOMBRE'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No hay empresas disponibles</option>";
                        }
                        ?>
                    </select>
                    <span>Empresa</span>
                </label>

                <!-- Nombre de la Cancha -->
                <label>
                    <input type="text" id="NOMBRECANCHA" name="NOMBRECANCHA" class="input" required>
                    <span>Nombre de la Cancha</span>
                </label>

                <!-- Foto -->
                <label>
                    <input type="file" id="FOTO" name="FOTO" class="input">
                    <span></span>
                </label>

                <!-- Tipo de Cancha -->
                <label>
                    <input type="text" id="TIPO" name="TIPO" class="input" required>
                    <span>Tipo de Cancha</span>
                </label>

                <!-- Cantidad de Jugadores -->
                <label>
                    <input type="number" id="CANTJUGADORES" name="CANTJUGADORES" class="input" required>
                    <span>Cantidad de Jugadores</span>
                </label>

                <!-- Precio por Hora -->
                <label>
                    <input type="text" id="PRECIOHORA" name="PRECIOHORA" class="input" required>
                    <span>Precio por Hora</span>
                </label>

                <!-- Botón para guardar -->
                <button type="submit" class="submit">Guardar</button>
                <button onclick="window.location.href='../canchas/list.php';" class="submit">Ver lista Canchas</button>
                
                <!-- Botón para cancelar -->
                <div class="signin">
                    <button onclick="window.location.href='../index.php';" class="text-red-500 hover:underline">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
