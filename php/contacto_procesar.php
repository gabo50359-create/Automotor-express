<?php
// Incluir conexión a BD
include "conexion.php";

// Variable para mensaje
$mensaje = "";

// Procesar formulario si es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensajeUsuario = trim($_POST['mensaje'] ?? '');
    
    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensajeUsuario)) {
        $mensaje = "❌ Por favor completa todos los campos obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "❌ El email no es válido";
    } else {
        // Insertar en la base de datos
        $insercion = $conexion->prepare("INSERT INTO contactos (nombre, email, telefono, asunto, mensaje) VALUES (?, ?, ?, ?, ?)");
        $insercion->bind_param("sssss", $nombre, $email, $telefono, $asunto, $mensajeUsuario);
        
        if ($insercion->execute()) {
            $mensaje = "✅ Tu mensaje fue enviado correctamente. Nos contactaremos pronto.";
            // Limpiar formulario
            $_POST = array();
        } else {
            $mensaje = "❌ Error al enviar el mensaje. Intenta de nuevo.";
        }
    }
}

$conexion->close();

// Retornar mensaje (si viene por AJAX)
echo $mensaje;
?>
