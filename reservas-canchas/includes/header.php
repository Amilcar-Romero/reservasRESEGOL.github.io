<?php
session_start();

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'canchabd';
$username = 'root';
$password = '';
$nombreRol = "Sin rol asignado";

// Obtener el rol del usuario si está logueado
if (isset($_SESSION['usuario'])) {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("
            SELECT roles.nombre 
            FROM usuario 
            INNER JOIN roles ON usuario.rol = roles.id 
            WHERE usuario.nombre = :nombreUsuario
        ");
        $stmt->bindParam(':nombreUsuario', $_SESSION['usuario'], PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $nombreRol = $result['nombre'];
        }
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Estilo Flowbite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar-brand img {
            height: 40px;
        }
        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #ccc;
        }
        .navbar-dark {
            background-color: #212529 !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://flowbite.com/docs/images/logo.svg" alt="Logo">
                <span class="ms-2">RESEGOL</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="../public/index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Acerca de</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Servicios</a>
                    </li>

                    <!-- Opciones según el rol -->
                    <?php if ($nombreRol === 'CLIENTE'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/reservas/list.php">Mis Reservas</a>
                        </li>
                    <?php elseif ($nombreRol === 'ADMIN'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="gestionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Gestiones
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="gestionesDropdown">
                                <li><a class="dropdown-item" href="../public/canchas/create.php">Gestionar Canchas</a></li>
                            </ul>
                        </li>
                    <?php elseif ($nombreRol === 'FULL'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="gestionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Gestiones
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="gestionesDropdown">
                                <li><a class="dropdown-item" href="../public/Empresas/create.php">Gestionar Empresas</a></li>
                                <li><a class="dropdown-item" href="../public/GestionarUsuarios/index.php">Gestionar Usuarios</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <!-- Botón de inicio/cierre de sesión -->
                    <?php if (!isset($_SESSION['usuario'])): ?>
                        <a href="../public/login.php" class="btn btn-primary me-2">Iniciar sesión</a>
                    <?php else: ?>
                        <span class="me-2">Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</span>
                        <a href="../public/logout.php" class="btn btn-outline-primary">Cerrar Sesión</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mt-5 pt-5">
        <h1>Bienvenido al sistema</h1>
        <p>Reserva tu Cancha Favorita y pasa un día inolvidable!</p>
    </main>
</body>
</html>
