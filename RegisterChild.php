<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Incluye la conexión a la base de datos
include 'config.php';

// Obtén los datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Verifica si los datos están presentes
if (!isset($data['nombres'], $data['apellidos'], $data['fecha_nacimiento'], $data['edad'], $data['user_id'])) {
    error_log("Datos recibidos incompletos: " . json_encode($data));  // Log para ver qué datos recibimos
    echo json_encode(["error" => "Datos incompletos o inválidos."]);
    exit();
}

$nombres = $data['nombres'];
$apellidos = $data['apellidos'];
$fechaNacimiento = $data['fecha_nacimiento'];
$edad = $data['edad'];
$userId = $data['user_id'];  // Obtener el user_id enviado desde el frontend

// Validar nombres y apellidos (solo letras)
if (!preg_match("/^[a-zA-Z\s]+$/", $nombres) || !preg_match("/^[a-zA-Z\s]+$/", $apellidos)) {
    echo json_encode(["error" => "El nombre y apellido deben contener solo letras."]);
    exit();
}

// Validar fecha de nacimiento (no puede ser futura)
if (strtotime($fechaNacimiento) > time()) {
    echo json_encode(["error" => "La fecha de nacimiento no puede ser futura."]);
    exit();
}

// Validar edad (debe ser un número entero)
if (!is_numeric($edad) || (int)$edad != $edad || $edad <= 0) {
    echo json_encode(["error" => "La edad debe ser un número válido."]);
    exit();
}

// Verifica si el user_id existe en la tabla 'usuarios'
$result = $conn->query("SELECT id FROM usuarios WHERE id = $userId");

if ($result->num_rows == 0) {
    echo json_encode(["error" => "El usuario no existe."]);
    exit();
}

// Realiza la lógica para insertar los datos en la base de datos
try {
    $stmt = $conn->prepare("INSERT INTO niños (nombres, apellidos, fecha_nacimiento, edad, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombres, $apellidos, $fechaNacimiento, $edad, $userId);  // Asegúrate de que 'edad' es un entero

    if ($stmt->execute()) {
        echo json_encode(["message" => "Niño registrado exitosamente."]);
    } else {
        error_log("Error al insertar datos: " . $stmt->error);  // Log para ver el error en la inserción
        echo json_encode(["error" => "Error al registrar al niño."]);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error en la base de datos: " . $e->getMessage());  // Log para ver el error
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
}

$conn->close();
?>
