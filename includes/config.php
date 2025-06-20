<?php
/**
 * Archivo de configuración general
 * Contiene constantes y funciones de utilidad para toda la aplicación
 */

// Iniciar la sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir constantes del sistema
define('BASE_URL', 'http://localhost/edulink/');
define('UPLOADS_DIR', $_SERVER['DOCUMENT_ROOT'] . '/edulink/uploads/');
define('UPLOADS_URL', BASE_URL . '/uploads/');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Función para sanitizar entradas
function limpiarDatos($datos) {
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    return $datos;
}

// Función para redireccionar
function redireccionar($url) {
    header('Location: ' . BASE_URL . $url);
    exit;
}

// Función para verificar si el usuario ha iniciado sesión
function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

// Función para verificar el rol del usuario
function tieneRol($rol) {
    if (!estaAutenticado()) {
        return false;
    }
    
    return $_SESSION['usuario_rol'] === $rol;
}

// Función para verificar si el usuario es profesor o administrador
function esProfesorOAdmin() {
    if (!estaAutenticado()) {
        return false;
    }
    
    return ($_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'administrador');
}

// Función para mostrar mensajes flash
function setMensaje($tipo, $mensaje) {
    $_SESSION['mensaje'] = [
        'tipo' => $tipo,
        'texto' => $mensaje
    ];
}

// Función para obtener y eliminar un mensaje flash
function getMensaje() {
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        return $mensaje;
    }
    return null;
}

// Función para generar un token CSRF
function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar un token CSRF
function verificarTokenCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}