<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include 'config.php'; // Archivo de configuración con la conexión a la base de datos

$data = json_decode(file_get_contents("php://input"), true);

// Verificación de los datos recibidos
if (!$data) {
    echo json_encode(["error" => "Datos inválidos."]);
    exit();
}

$email = trim($data['email']);
$nombre = trim($data['nombre']);
$password = $data['password'];

// Validación de email: formato adecuado y dominios permitidos
$dominios_permitidos = ['gmail.com', 'hotmail.com', 'outlook.com'];

// Comprobamos si el correo tiene un formato válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "El email no es válido."]);
    exit();
}

// Extraemos el dominio del correo para verificar si está en la lista permitida
$dominio = explode('@', $email)[1]; // Obtener el dominio después de '@'

if (!in_array($dominio, $dominios_permitidos)) {
    echo json_encode(["error" => "El dominio del email no está permitido."]);
    exit();
}

// Validación del nombre antes del '@' en el email (coherencia del nombre)
$nombre_email = explode('@', $email)[0]; // Parte antes del '@'

// Validación de nombre para evitar secuencias aleatorias de letras
// Comprobamos si el nombre es solo una secuencia de letras repetitivas o aleatorias (no tiene sentido)
if (preg_match("/^[a-zA-Z]{6,}$/", $nombre_email) && !preg_match("/[aeiou]{3,}/", $nombre_email)) {
    echo json_encode(["error" => "El nombre del email no debe ser solo una secuencia aleatoria de letras."]);
    exit();
}

// Validación del nombre: solo letras y espacios
if (empty($nombre) || !preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
    echo json_encode(["error" => "El nombre debe contener solo letras y espacios."]);
    exit();
}

// Validación de la contraseña: debe tener al menos 8 caracteres
if (empty($password) || strlen($password) < 8) {
    echo json_encode(["error" => "La contraseña debe tener al menos 8 caracteres."]);
    exit();
}

// Encriptar la contraseña
$password_hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Verificar si el email ya está registrado en la base de datos
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["error" => "El email ya está registrado."]);
    } else {
        // Insertar el nuevo usuario en la base de datos
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $password_hash);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Registro exitoso."]);
        } else {
            error_log("Error al registrar usuario: " . $stmt->error);
            echo json_encode(["error" => "Error al registrar el usuario."]);
        }
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Excepción: " . $e->getMessage());
    echo json_encode(["error" => "Error en el servidor."]);
}

$conn->close();
?>
