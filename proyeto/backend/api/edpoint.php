<?php
// ==== CONFIGURACIÓN CORS ====
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// Si es preflight OPTIONS, responder y terminar
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// ==== CONEXIÓN A LA BD ====
require_once "../dbconexion/db_conexion.php";
$conn = dbconexion::conectar();

// Obtener acción por GET o POST
$accion = $_POST["action"] ?? $_GET["action"] ?? '';

// ==== MANEJO DE SOLICITUDES ====

// Función para enviar respuestas JSON estandarizadas
function responder($success, $message, $data = null) {
    $response = ["success" => $success, "message" => $message];
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response);
    exit();
}

// Si no hay acción pero se pide un libro por ID (para la página de eliminar)
if ($accion === '' && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_libro'])) {
    $accion = 'obtener_libro';
}

switch ($accion) {
    // === ACCIONES DE LIBROS ===
    case 'mostrar_libros':
        $stmt = $conn->prepare("SELECT id_libro, titulo, autor, anio, categoria, stock FROM libros");
        $stmt->execute();
        $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        responder(true, "Libros obtenidos correctamente.", $libros);
        break;

    case 'obtener_libro':
        $id_libro = $_GET['id_libro'] ?? 0;
        $stmt = $conn->prepare("SELECT * FROM libros WHERE id_libro = :id_libro");
        $stmt->bindParam(':id_libro', $id_libro);
        $stmt->execute();
        $libro = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($libro) {
            responder(true, "Libro encontrado.", $libro);
        } else {
            responder(false, "Libro no encontrado.");
        }
        break;

    case 'crear_libro':
        $titulo = $_POST['titulo'] ?? '';
        $autor = $_POST['autor'] ?? '';
        $anio = $_POST['anio'] ?? 0;
        $categoria = $_POST['categoria'] ?? '';
        $stock = $_POST['stock'] ?? 0;

        $stmt = $conn->prepare("INSERT INTO libros (titulo, autor, anio, categoria, stock) VALUES (:titulo, :autor, :anio, :categoria, :stock)");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':autor', $autor);
        $stmt->bindParam(':anio', $anio);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':stock', $stock);

        if ($stmt->execute()) {
            responder(true, "Libro creado con éxito.");
        } else {
            responder(false, "Error al crear el libro.");
        }
        break;

    case 'editar_libro':
        $id_libro = $_POST['id_libro'] ?? 0;
        $titulo = $_POST['titulo'] ?? '';
        $autor = $_POST['autor'] ?? '';
        $anio = $_POST['anio'] ?? 0;
        $categoria = $_POST['categoria'] ?? '';
        $stock = $_POST['stock'] ?? 0;

        $stmt = $conn->prepare("UPDATE libros SET titulo = :titulo, autor = :autor, anio = :anio, categoria = :categoria, stock = :stock WHERE id_libro = :id_libro");
        $stmt->bindParam(':id_libro', $id_libro);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':autor', $autor);
        $stmt->bindParam(':anio', $anio);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':stock', $stock);

        if ($stmt->execute()) {
            responder(true, "Libro actualizado con éxito.");
        } else {
            responder(false, "Error al actualizar el libro.");
        }
        break;

    case 'eliminar_libro':
        $id_libro = $_POST['id_libro'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM libros WHERE id_libro = :id_libro");
        $stmt->bindParam(':id_libro', $id_libro);

        if ($stmt->execute()) {
            responder(true, "Libro eliminado con éxito.");
        } else {
            responder(false, "Error al eliminar el libro.");
        }
        break;

    // === ACCIONES DE USUARIOS (para los selects de préstamos) ===
    case 'mostrar_usuarios':
        $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        responder(true, "Usuarios obtenidos correctamente.", $usuarios);
        break;

    // === ACCIONES DE PRÉSTAMOS ===
    case 'mostrar_prestamos':
        $query = "SELECT p.id_prestamo, u.nombre as usuario, l.titulo as libro, p.fecha_prestamo, p.fecha_devolucion, p.estado 
                  FROM prestamos p
                  JOIN usuarios u ON p.id_usuario = u.id_usuario
                  JOIN libros l ON p.id_libro = l.id_libro";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        responder(true, "Préstamos obtenidos correctamente.", $prestamos);
        break;

    case 'obtener_prestamo':
        $id_prestamo = $_GET['id_prestamo'] ?? 0;
        $stmt = $conn->prepare("SELECT * FROM prestamos WHERE id_prestamo = :id_prestamo");
        $stmt->bindParam(':id_prestamo', $id_prestamo);
        $stmt->execute();
        $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($prestamo) {
            responder(true, "Préstamo encontrado.", $prestamo);
        } else {
            responder(false, "Préstamo no encontrado.");
        }
        break;

    case 'crear_prestamo':
        $id_usuario = $_POST['id_usuario'] ?? 0;
        $id_libro = $_POST['id_libro'] ?? 0;
        $fecha_prestamo = $_POST['fecha_prestamo'] ?? '';
        $fecha_devolucion = $_POST['fecha_devolucion'] ?? '';
        $estado = $_POST['estado'] ?? 'Pendiente';

        $stmt = $conn->prepare("INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, estado) VALUES (:id_usuario, :id_libro, :fecha_prestamo, :fecha_devolucion, :estado)");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_libro', $id_libro);
        $stmt->bindParam(':fecha_prestamo', $fecha_prestamo);
        $stmt->bindParam(':fecha_devolucion', $fecha_devolucion);
        $stmt->bindParam(':estado', $estado);

        if ($stmt->execute()) {
            responder(true, "Préstamo creado con éxito.");
        } else {
            responder(false, "Error al crear el préstamo.");
        }
        break;

    case 'editar_prestamo':
        $id_prestamo = $_POST['id_prestamo'] ?? 0;
        $id_usuario = $_POST['id_usuario'] ?? 0;
        $id_libro = $_POST['id_libro'] ?? 0;
        $fecha_prestamo = $_POST['fecha_prestamo'] ?? '';
        $fecha_devolucion = $_POST['fecha_devolucion'] ?? '';
        $estado = $_POST['estado'] ?? 'Pendiente';

        $stmt = $conn->prepare("UPDATE prestamos SET id_usuario = :id_usuario, id_libro = :id_libro, fecha_prestamo = :fecha_prestamo, fecha_devolucion = :fecha_devolucion, estado = :estado WHERE id_prestamo = :id_prestamo");
        $stmt->bindParam(':id_prestamo', $id_prestamo);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_libro', $id_libro);
        $stmt->bindParam(':fecha_prestamo', $fecha_prestamo);
        $stmt->bindParam(':fecha_devolucion', $fecha_devolucion);
        $stmt->bindParam(':estado', $estado);

        if ($stmt->execute()) {
            responder(true, "Préstamo actualizado con éxito.");
        } else {
            responder(false, "Error al actualizar el préstamo.");
        }
        break;

    case 'eliminar_prestamo':
        $id_prestamo = $_POST['id_prestamo'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM prestamos WHERE id_prestamo = :id_prestamo");
        $stmt->bindParam(':id_prestamo', $id_prestamo);

        if ($stmt->execute()) {
            responder(true, "Préstamo eliminado con éxito.");
        } else {
            responder(false, "Error al eliminar el préstamo.");
        }
        break;

    default:
        responder(false, "Acción no válida.");
        break;
}
