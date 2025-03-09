<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'config.php';

// Obtener la fecha actual en formato YYYY-MM-DD
$current_date = date('Y-m-d');

// Consulta para obtener las alertas de la fecha actual
$sql = "SELECT sound_level, timestamp 
        FROM sound_data 
        WHERE DATE(timestamp) = '$current_date' 
        ORDER BY timestamp DESC";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Devolver los datos en formato JSON
echo json_encode($data);

$conn->close();
?>
