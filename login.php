<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include 'config.php';

// Leer los datos JSON de la solicitud
$data = json_decode(file_get_contents("php://input"));

// Depuración: imprimir el contenido de la solicitud
error_log("Datos recibidos: " . print_r($data, true));

if ($data) {
    $email = trim($data->email);  // Cambié 'correo' por 'email' para que coincida con el frontend
    $password = $data->password;

    // Validación de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Email no válido"]);
        exit();
    }

    try {
        // Preparar la consulta para buscar el email en la base de datos
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email); // Usamos 'email' que es el campo correcto en la base de datos
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verificar la contraseña
            if (password_verify($password, $row['password'])) {
                echo json_encode([
                    "message" => "Inicio de sesión exitoso",
                    "user_id" => $row['id'],
                    "name" => $row['nombre'],  // Puedes incluir más detalles si los necesitas
                ]);
            } else {
                echo json_encode(["error" => "Contraseña incorrecta"]);
            }
        } else {
            echo json_encode(["error" => "No se encontró el usuario"]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Datos inválidos"]);
}

$conn->close();
?>
