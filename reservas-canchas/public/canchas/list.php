<?php
// Incluir la conexión a la base de datos
require_once '../../config/db.php';

// Obtener todas las canchas de la base de datos con PDO
$sql = "SELECT C.ID, C.NOMBRECANCHA, C.TIPO, C.CANTJUGADORES, C.PRECIOHORA, C.FOTO, E.NOMBRE AS NOMBRE_EMPRESA 
        FROM CANCHA C
        INNER JOIN EMPRESA E ON C.IDEMPRESA = E.ID";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$canchas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Canchas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 text-center">Listado de Canchas</h1>
        
        <!-- Botón para crear nueva cancha -->
        <div class="mb-4 text-right">
            <a href="create.php" class="bg-blue-500 text-white px-4 py-2 rounded">Crear Nueva Cancha</a>
        </div>

        <!-- Tabla de canchas -->
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Tipo</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Jugadores</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Precio</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Empresa</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Foto</th>
                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($canchas)) : ?>
                    <?php foreach ($canchas as $cancha) : ?>
                        <tr class="border-b">
                            <td class="px-6 py-4 text-sm text-gray-700"><?= $cancha['ID'] ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($cancha['NOMBRECANCHA']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($cancha['TIPO']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($cancha['CANTJUGADORES']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700">$<?= number_format($cancha['PRECIOHORA'], 2) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($cancha['NOMBRE_EMPRESA']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php if ($cancha['FOTO']) : ?>
                                    <img src="<?= htmlspecialchars($cancha['FOTO']) ?>" alt="Foto" class="h-16 w-16 object-cover rounded">
                                <?php else : ?>
                                    No disponible
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700">
                                <a href="edit.php?id=<?= $cancha['ID'] ?>" class="text-blue-500 hover:underline">Editar</a> |
                                <a href="delete.php?id=<?= $cancha['ID'] ?>" class="text-red-500 hover:underline" onclick="return confirm('¿Está seguro de eliminar esta cancha?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-700">No hay canchas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
