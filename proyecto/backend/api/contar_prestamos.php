<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../dbconexion/db_conexion.php";

try {
    $conn = dbconexion::conectar();

    $sql = "SELECT COUNT(*) AS total FROM prestamos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total" => $row["total"]
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
