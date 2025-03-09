<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT id, nombres, edad, nivel FROM niños";
    $stmt = $pdo->query($query);

    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["children" => $children]);

} catch (PDOException $e) {
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}

$pdo = null;
?>
