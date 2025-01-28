<?php
require_once '../../config/db.php'; // Ruta correcta de la conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $rol = intval($_POST['rol']);

    if (!empty($nombre) && !empty($apellido) && !empty($correo) && !empty($password) && $rol > 0) {
        $passwordCifrada = password_hash($password, PASSWORD_BCRYPT);

        $query = $pdo->prepare("
            INSERT INTO USUARIO (NOMBRE, APELLIDO, CORREO, CONTRASEÑA, ROL) 
            VALUES (:nombre, :apellido, :correo, :password, :rol)
        ");

        if ($query->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'password' => $passwordCifrada,
            'rol' => $rol
        ])) {
            header('Location: index.php?success=Usuario agregado correctamente');
            exit();
        } else {
            header('Location: index.php?error=No se pudo agregar el usuario');
            exit();
        }
    } else {
        header('Location: index.php?error=Por favor completa todos los campos correctamente');
        exit();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombreRol'])) {
    $nombreRol = trim($_POST['nombreRol']);

    if (!empty($nombreRol)) {
        $query = $pdo->prepare("INSERT INTO ROLES (NOMBRE) VALUES (:nombre)");
        if ($query->execute(['nombre' => $nombreRol])) {
            header('Location: index.php?success=Rol agregado correctamente');
            exit();
        } else {
            header('Location: index.php?error=No se pudo agregar el rol');
            exit();
        }
    } else {
        header('Location: index.php?error=Por favor completa el campo correctamente');
        exit();
    }
}
