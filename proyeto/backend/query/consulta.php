<?php
require_once __DIR__ . "/../dbconexion/db_conexion.php";

class Biblioteca {

    // ---------------------- USUARIOS ----------------------
    public static function mostrarUsuarios() {
        $conn = dbconexion::conectar();
        $query = "SELECT * FROM usuarios";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function crearUsuario($nombre, $apellido, $email, $telefono) {
        $conn = dbconexion::conectar();
        $query = "INSERT INTO usuarios (nombre, apellido, email, telefono) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $nombre);
        $stmt->bindParam(2, $apellido);
        $stmt->bindParam(3, $email);
        $stmt->bindParam(4, $telefono);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function eliminarUsuario($id) {
        $conn = dbconexion::conectar();
        $query = "DELETE FROM usuarios WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function editarUsuario($id, $nombre, $apellido, $email, $telefono) {
        $conn = dbconexion::conectar();
        $query = "UPDATE usuarios SET nombre=?, apellido=?, email=?, telefono=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $nombre);
        $stmt->bindParam(2, $apellido);
        $stmt->bindParam(3, $email);
        $stmt->bindParam(4, $telefono);
        $stmt->bindParam(5, $id, PDO::PARAM_INT);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function obtenerUsuarioPorId($id) {
        $conn = dbconexion::conectar();
        $query = "SELECT * FROM usuarios WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode(['success' => !!$usuario, 'data' => $usuario]);
    }

    // ---------------------- LIBROS ----------------------
    public static function mostrarLibros() {
        $conn = dbconexion::conectar();
        $query = "SELECT * FROM libros";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function crearLibro($titulo, $autor, $editorial, $anio, $genero, $cantidad) {
        $conn = dbconexion::conectar();
        $query = "INSERT INTO libros (titulo, autor, editorial, anio, genero, cantidad) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $titulo);
        $stmt->bindParam(2, $autor);
        $stmt->bindParam(3, $editorial);
        $stmt->bindParam(4, $anio, PDO::PARAM_INT);
        $stmt->bindParam(5, $genero);
        $stmt->bindParam(6, $cantidad, PDO::PARAM_INT);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function eliminarLibro($id) {
        $conn = dbconexion::conectar();
        $query = "DELETE FROM libros WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function editarLibro($id, $titulo, $autor, $editorial, $anio, $genero, $cantidad) {
        $conn = dbconexion::conectar();
        $query = "UPDATE libros SET titulo=?, autor=?, editorial=?, anio=?, genero=?, cantidad=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $titulo);
        $stmt->bindParam(2, $autor);
        $stmt->bindParam(3, $editorial);
        $stmt->bindParam(4, $anio, PDO::PARAM_INT);
        $stmt->bindParam(5, $genero);
        $stmt->bindParam(6, $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(7, $id, PDO::PARAM_INT);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function obtenerLibroPorId($id) {
        $conn = dbconexion::conectar();
        $query = "SELECT * FROM libros WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $libro = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode(['success' => !!$libro, 'data' => $libro]);
    }

    // ---------------------- PRÉSTAMOS ----------------------
    public static function mostrarPrestamos() {
        $conn = dbconexion::conectar();
        $query = "SELECT p.*, l.titulo, u.nombre AS usuario FROM prestamos p
                  JOIN libros l ON p.id_libro = l.id
                  JOIN usuarios u ON p.id_usuario = u.id";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function crearPrestamo($id_libro, $id_usuario, $fecha_prestamo, $fecha_devolucion, $estado) {
        $conn = dbconexion::conectar();
        $query = "INSERT INTO prestamos (id_libro, id_usuario, fecha_prestamo, fecha_devolucion, estado)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id_libro, PDO::PARAM_INT);
        $stmt->bindParam(2, $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(3, $fecha_prestamo);
        $stmt->bindParam(4, $fecha_devolucion);
        $stmt->bindParam(5, $estado);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function eliminarPrestamo($id) {
        $conn = dbconexion::conectar();
        $query = "DELETE FROM prestamos WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function editarPrestamo($id, $id_libro, $id_usuario, $fecha_prestamo, $fecha_devolucion, $estado) {
        $conn = dbconexion::conectar();
        $query = "UPDATE prestamos SET id_libro=?, id_usuario=?, fecha_prestamo=?, fecha_devolucion=?, estado=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id_libro, PDO::PARAM_INT);
        $stmt->bindParam(2, $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(3, $fecha_prestamo);
        $stmt->bindParam(4, $fecha_devolucion);
        $stmt->bindParam(5, $estado);
        $stmt->bindParam(6, $id, PDO::PARAM_INT);
        $stmt->execute();
        return json_encode(['success' => $stmt->rowCount() > 0]);
    }

    public static function obtenerPrestamoPorId($id) {
        $conn = dbconexion::conectar();
        $query = "SELECT * FROM prestamos WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode(['success' => !!$prestamo, 'data' => $prestamo]);
    }
}
?>
