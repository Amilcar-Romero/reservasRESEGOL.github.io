<?php
require_once '../../config/db.php'; // Ajusta la ruta de conexión a la base de datos

// Recibir los parámetros de búsqueda y rol
$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '0';

// Construir la consulta SQL
$sql = "SELECT u.ID, u.NOMBRE, u.APELLIDO, u.CORREO, r.NOMBRE AS ROL 
        FROM USUARIO u 
        JOIN ROLES r ON u.ROL = r.ID WHERE 1=1";

// Filtrar por nombre o apellido si se ingresa un texto de búsqueda
if (!empty($search)) {
    $sql .= " AND (u.NOMBRE LIKE :search OR u.APELLIDO LIKE :search)";
}

// Filtrar por rol si se selecciona un rol específico
if ($role !== '0') {
    $sql .= " AND u.ROL = :role";
}

// Preparar la consulta
$query = $pdo->prepare($sql);

// Vincular los parámetros
if (!empty($search)) {
    $query->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

if ($role !== '0') {
    $query->bindValue(':role', $role, PDO::PARAM_INT);
}

$query->execute();
$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

// Generar el HTML para la tabla de usuarios
foreach ($usuarios as $usuario) {
    echo "<tr>
        <td class='border border-gray-300 p-2'>{$usuario['ID']}</td>
        <td class='border border-gray-300 p-2'>{$usuario['NOMBRE']}</td>
        <td class='border border-gray-300 p-2'>{$usuario['APELLIDO']}</td>
        <td class='border border-gray-300 p-2'>{$usuario['CORREO']}</td>
        <td class='border border-gray-300 p-2'>{$usuario['ROL']}</td>
        <td class='border border-gray-300 p-2'>
            <button onclick='openEditModal({$usuario['ID']}, \"{$usuario['NOMBRE']}\", \"{$usuario['APELLIDO']}\", \"{$usuario['CORREO']}\", \"{$usuario['ROL']}\")' class='bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-700'>Editar</button>
            <button onclick='confirmDelete({$usuario['ID']})' class='bg-red-500 text-white py-2 px-4 rounded hover:bg-red-700 ml-2'>Eliminar</button>
        </td>
    </tr>";
}
?>
