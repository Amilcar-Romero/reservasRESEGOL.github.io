<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table img {
            border-radius: 5px;
        }
        .btn-success {
            background-color: royalblue;
            border-color: royalblue;
        }
        .btn-success:hover {
            background-color: rgb(56, 90, 194);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Listado de Empresas</h2>
            <a href="create.php" class="btn btn-success">Agregar Empresa</a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Coordenadas</th>
                    <th>Logo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once '../../config/db.php';

                try {
                    $sql = "SELECT * FROM EMPRESA";
                    $stmt = $pdo->query($sql);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['NOMBRE']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['DIRECCION']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['TELEFONO']) . "</td>";
                        echo "<td>" . round($row['LATITUD'], 6) . ", " . round($row['LONGITUD'], 6) . "</td>";

                        if (!empty($row['LOGO'])) {
                            $logoPath = htmlspecialchars($row['LOGO']);
                            echo "<td><img src='$logoPath' alt='Logo' width='50' height='50'></td>";
                        } else {
                            echo "<td>No disponible</td>";
                        }

                        echo "<td>
                                <a href='edit.php?id=" . htmlspecialchars($row['ID']) . "' class='btn btn-warning btn-sm'>Editar</a>
                                <a href='delete.php?id=" . htmlspecialchars($row['ID']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de eliminar esta empresa?\");'>Eliminar</a>
                              </td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7' class='text-center'>Error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
