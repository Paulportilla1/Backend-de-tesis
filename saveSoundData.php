<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'config.php'; 


$data = json_decode(file_get_contents("php://input"), true);

// Verificar si los datos se han recibido correctamente
if(isset($data['soundLevel'])) {
    $soundLevel = $data['soundLevel'];

    // Insertar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO sound_data (sound_level) VALUES (?)");
    $stmt->bind_param("i", $soundLevel);
    $stmt->execute();

    // Responder al cliente
    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Datos recibidos y guardados correctamente"]);
    } else {
        echo json_encode(["message" => "Error al guardar los datos"]);
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["message" => "No se recibieron datos válidos"]);
}
?>