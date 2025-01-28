<?php
session_start();
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!empty($correo) && !empty($password)) {
        $query = $pdo->prepare("SELECT * FROM USUARIO WHERE CORREO = :correo");
        $query->execute(['correo' => $correo]);
        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $password == $usuario['CONTRASEÑA']) {
            $_SESSION['usuario'] = $usuario['NOMBRE'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Correo o contraseña incorrectos';
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = filter_var(trim($_POST['correo_reg']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password_reg']);  // Se guarda sin encriptar
    $rol = intval($_POST['rol']);  // Asegúrate de que el rol sea un valor entero válido

    if (!empty($nombre) && !empty($apellido) && !empty($correo) && !empty($password) && $rol > 0) {
        $query = $pdo->prepare("INSERT INTO USUARIO (NOMBRE, APELLIDO, CORREO, CONTRASEÑA, ROL) VALUES (:nombre, :apellido, :correo, :password, :rol)");
        $success = $query->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'password' => $password,
            'rol' => $rol
        ]) ? 'Registro exitoso. Ahora puedes iniciar sesión.' : 'Error al registrar el usuario.';
    } else {
        $error = 'Por favor, completa todos los campos correctamente.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-800 flex items-center justify-center h-screen" style="background-image: url('https://i0.wp.com/www.construcanchas.com/wp-content/uploads/2021/03/fondo-3.jpg?resize=1060%2C571&ssl=1'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="bg-white p-8 rounded-xl shadow-2xl max-w-4xl flex">
        <!-- Formulario de inicio de sesión -->
        <div class="w-1/2 p-8">
            <h1 class="text-xl font-bold mb-6 text-center">Iniciar Sesión</h1>
            
            <?php if ($error): ?>
                <div class="text-red-500 text-sm mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="text-green-500 text-sm mb-4"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" action="login.php" class="space-y-5">
                <div>
                    <label for="correo" class="block text-sm font-semibold text-gray-700">Correo:</label>
                    <input type="email" id="correo" name="correo" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña:</label>
                    <input type="password" id="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" name="login" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                    Ingresar
                </button>
            </form>

            <div class="text-center mt-4">
                <button id="openRegisterModal" class="text-blue-500 hover:underline">¿No tienes una cuenta? Regístrate</button>
            </div>
        </div>

        <!-- Imagen decorativa -->
        <div class="w-1/2 flex items-center justify-center bg-blue-500 text-white rounded-r-xl">
         <div class="text-center">
        <img src="https://imagenes.elpais.com/resizer/v2/KGAHKN5H5NMAFFIIEWNNVSFOCI.jpg?auth=e6a29dd37e5fc8a137dcee76fbda770699e146db304fc5660db9a39e5687b707&width=414" 
             alt="Imagen decorativa" 
             class="w-48 h-48 mx-auto mb-4">
        <h2 class="text-2xl font-bold mb-4">Bienvenido</h2>
        <p class="mb-6">Inicia sesión o regístrate para disfrutar de nuestros servicios.</p>
     </div>
    </div>

    </div>

    <!-- Modal de registro -->
    <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-6 text-center">Regístrate</h2>
            <form method="post" action="login.php" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-sm font-semibold text-gray-700">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="apellido" class="block text-sm font-semibold text-gray-700">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="correo_reg" class="block text-sm font-semibold text-gray-700">Correo:</label>
                    <input type="email" id="correo_reg" name="correo_reg" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="password_reg" class="block text-sm font-semibold text-gray-700">Contraseña:</label>
                    <input type="password" id="password_reg" name="password_reg" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <input type="hidden" id="rol" name="rol" value="3">
                <button type="submit" name="register" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg">
                    Registrarse
                </button>
            </form>
            <div class="text-center mt-4">
                <button id="closeRegisterModal" class="text-red-500 hover:underline">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        // Lógica para abrir y cerrar el modal de registro
        document.getElementById('openRegisterModal').addEventListener('click', function() {
            document.getElementById('registerModal').classList.remove('hidden');
        });

        document.getElementById('closeRegisterModal').addEventListener('click', function() {
            document.getElementById('registerModal').classList.add('hidden');
        });
    </script>
</body>
</html>
