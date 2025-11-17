<?php
require_once __DIR__ . "/db_conexion.php";

$conn = dbconexion::conectar();

if($conn){
    echo "✅ Conexión exitosa a la base de datos";
}else{
    echo "❌ No se pudo conectar";
} 
?>