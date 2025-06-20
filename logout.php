<?php
/**
 * Cierre de sesión
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Cerrar sesión
cerrarSesion();

// El cierre de sesión redirige automáticamente al inicio
?>