<?php
session_start();
require_once '../../config/db.php'; // Ajusta esta ruta según tu estructura


// Obtener todos los roles de la base de datos
$queryRoles = $pdo->prepare("SELECT * FROM ROLES");
$queryRoles->execute();
$roles = $queryRoles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 p-4 text-white">
        <h1 class="text-center text-xl font-bold">Gestión de Usuarios</h1>
    </header>

    <main class="p-8">
        <section>
            <!-- Botones para agregar usuarios -->
            <div class="flex justify-between mb-6">
                <button onclick="toggleModal('addUserModal')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Agregar Usuario
                </button>
            </div>

            <!-- Filtro por Nombre o Apellido -->
            <div class="mb-6">
                <input type="text" id="searchInput" placeholder="Buscar por nombre o apellido..." class="p-2 border border-gray-300 rounded w-full">
            </div>

            <!-- Desplegable de Roles -->
            <div class="mb-6">
                <select id="roleFilter" class="p-2 border border-gray-300 rounded w-full">
                    <option value="0">Todos los Roles</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['ID'] ?>"><?= $rol['NOMBRE'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tabla de Usuarios -->
            <div>
                <h2 class="text-2xl font-bold mb-4">Lista de Usuarios</h2>
                <table class="w-full table-auto border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 p-2">ID</th>
                            <th class="border border-gray-300 p-2">Nombre</th>
                            <th class="border border-gray-300 p-2">Apellido</th>
                            <th class="border border-gray-300 p-2">Correo</th>
                            <th class="border border-gray-300 p-2">Rol</th>
                            <th class="border border-gray-300 p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="userTable">
                        <?php
                        // Obtener usuarios de la base de datos
                        $query = $pdo->prepare("SELECT u.ID, u.NOMBRE, u.APELLIDO, u.CORREO, r.NOMBRE AS ROL FROM USUARIO u JOIN ROLES r ON u.ROL = r.ID");
                        $query->execute();
                        $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);

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
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal de Agregar Usuario -->
    <div id="addUserModal" class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-2xl font-bold mb-4">Agregar Usuario</h2>
            <form action="add.php" method="POST">
                <!-- Campos para agregar usuario -->
                <label for="addNombre" class="block mb-2">Nombre:</label>
                <input type="text" id="addNombre" name="nombre" class="mb-4 p-2 border border-gray-300 rounded w-full" required>

                <label for="addApellido" class="block mb-2">Apellido:</label>
                <input type="text" id="addApellido" name="apellido" class="mb-4 p-2 border border-gray-300 rounded w-full" required>

                <label for="addCorreo" class="block mb-2">Correo:</label>
                <input type="email" id="addCorreo" name="correo" class="mb-4 p-2 border border-gray-300 rounded w-full" required>

                <label for="addPassword" class="block mb-2">Contraseña:</label>
                <input type="password" id="addPassword" name="password" class="mb-4 p-2 border border-gray-300 rounded w-full" required>

                <label for="addRol" class="block mb-2">Rol:</label>
                <select id="addRol" name="rol" class="mb-4 p-2 border border-gray-300 rounded w-full" required>
                    <option value="">Seleccionar rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['ID'] ?>"><?= $rol['NOMBRE'] ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="button" onclick="toggleModal('addUserModal')" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-700">Cancelar</button>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Guardar</button>
            </form>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div id="editModal" class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-2xl font-bold mb-4">Editar Usuario</h2>
            <form id="editForm">
                <input type="hidden" id="userId" name="userId">
                <label for="nombre" class="block mb-2">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="mb-4 p-2 border border-gray-300 rounded w-full" required>
                
                <label for="apellido" class="block mb-2">Apellido:</label>
                <input type="text" id="apellido" name="apellido" class="mb-4 p-2 border border-gray-300 rounded w-full" required>
                
                <label for="correo" class="block mb-2">Correo:</label>
                <input type="email" id="correo" name="correo" class="mb-4 p-2 border border-gray-300 rounded w-full" required>
                
                <label for="rol" class="block mb-2">Rol:</label>
                <select id="rol" name="rol" class="mb-4 p-2 border border-gray-300 rounded w-full" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['ID'] ?>"><?= $rol['NOMBRE'] ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Guardar Cambios</button>
                <button type="button" onclick="closeModal()" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-700">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        // Función para mostrar u ocultar el modal
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden'); // Alterna la clase 'hidden' para mostrar u ocultar el modal
}

        // Función para mostrar el modal de edición y cargar los datos
        function openEditModal(id, nombre, apellido, correo, rol) {
            document.getElementById('userId').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('apellido').value = apellido;
            document.getElementById('correo').value = correo;
            document.getElementById('rol').value = rol;
            document.getElementById('editModal').classList.remove('hidden');
        }

        // Función para cerrar el modal de edición
        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Manejar el envío del formulario con AJAX para editar usuario
        document.getElementById('editForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevenir que el formulario se envíe de forma tradicional

            const formData = new FormData(this);

            fetch('update.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Obtener la respuesta en formato JSON
            .then(data => {
                if (data.message) {
                    alert(data.message);  // Mostrar mensaje de éxito
                    location.reload();     // Recargar la página para reflejar los cambios
                } else {
                    alert(data.error || 'Ocurrió un error al actualizar los datos.');
                }
            })
            .catch(error => {
                alert('Error en la solicitud: ' + error);
            });
        });

        // Confirmar antes de eliminar el usuario
        function confirmDelete(userId) {
            if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                window.location.href = 'delete.php?id=' + userId;
            }
        }
    </script>
    <script>
   // Función para filtrar la tabla por nombre/apellido y rol
function filterTable() {
    const searchInput = document.getElementById('searchInput').value;
    const roleFilter = document.getElementById('roleFilter').value;

    // Hacer una solicitud AJAX a filter.php
    $.ajax({
        url: 'filter.php',
        type: 'GET',
        data: {
            search: searchInput,
            role: roleFilter
        },
        success: function(response) {
            // Actualizar el contenido de la tabla con la respuesta
            $('#userTable').html(response);
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
            alert('Error al filtrar los usuarios.');
        }
    });
}

// Llamamos a filterTable cada vez que cambian los filtros
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('roleFilter').addEventListener('change', filterTable);

// Llamamos a filterTable al cargar la página para aplicar filtros iniciales
window.onload = filterTable;


    </script>
</body>
</html>
