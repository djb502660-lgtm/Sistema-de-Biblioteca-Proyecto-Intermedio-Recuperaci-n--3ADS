<?php 

class user{
    
    public static function loginUsuario($email, $password){
        $conn=dbconexion::conectar();
        $query="SELECT * FROM usuarios WHERE email=?";
        $stmt=$conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar la contraseña hasheada
            if (password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['id'] = $row['id'];
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['email'] = $row['email'];
                return json_encode(['success' => true, 'message' => 'Usuario logueado correctamente']);
            }else{
                return json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
            }
            
        }else{
            return json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        }
     
    }

    public static function registrarUsuario($nombre, $apellido, $email, $usuario, $password){
        $conn = dbconexion::conectar();

        // Verificar si el email o usuario ya existen
        $query_check = "SELECT * FROM usuarios WHERE email = ? OR nombre_usuario = ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(1, $email);
        $stmt_check->bindParam(2, $usuario);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            return json_encode(['success' => false, 'message' => 'El correo electrónico o el nombre de usuario ya están en uso.']);
        }

        // Insertar nuevo usuario
        try {
            $query = "INSERT INTO usuarios (nombre, apellido, email, nombre_usuario, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(1, $nombre);
            $stmt->bindParam(2, $apellido);
            $stmt->bindParam(3, $email);
            $stmt->bindParam(4, $usuario);
            $stmt->bindParam(5, $password);
            $stmt->execute();

            return json_encode(['success' => $stmt->rowCount() > 0, 'message' => 'Usuario registrado con éxito.']);
        } catch (PDOException $e) {
            return json_encode(['success' => false, 'message' => 'Error al registrar el usuario.', 'error' => $e->getMessage()]);
        }
    }
}


?>