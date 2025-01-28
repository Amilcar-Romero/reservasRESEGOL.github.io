<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'canchabd'; // Asegúrate de que este sea el nombre correcto de tu base de datos
$username = 'root';
$password = ''; // Deja vacío si no usas contraseña en tu MySQL

header('Content-Type: application/json'); // Configura el tipo de contenido como JSON

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener las ubicaciones
    $stmt = $pdo->query("SELECT NOMBRE, LATITUD, LONGITUD FROM empresa");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver ubicaciones en formato JSON
    echo json_encode($locations);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
}
?>
