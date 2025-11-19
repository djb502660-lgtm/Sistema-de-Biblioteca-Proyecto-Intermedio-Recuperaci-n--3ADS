<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Conexión a la base de datos
require_once __DIR__ . "/../dbconexion/db_conexion.php";

// Validar acción
$accion = $_GET["accion"] ?? null;

if (!$accion) {
    echo json_encode([
        "status" => "error",
        "message" => "No se especificó una acción."
    ]);
    exit;
}

try {
    $conn = dbconexion::conectar();
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error de conexión: " . $e->getMessage()
    ]);
    exit;
}

switch ($accion) {

        case "listar_usuarios":
        try {
            $sql = "SELECT id, nombre, correo, telefono FROM usuarios";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "usuarios" => $usuarios
            ]);

        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
        break;

}

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total" => $data["total"]
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
