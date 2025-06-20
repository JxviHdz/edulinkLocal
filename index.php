<?php
/**
 * Página principal de la aplicación EduLink
 * Muestra la página de inicio o redirige al login si no hay sesión
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Incluir el header
require_once 'views/partials/header.php';

// Si no hay sesión, mostrar página de bienvenida
if (!estaAutenticado()) {
    require_once 'views/bienvenida.php';
} else {
    // Si hay sesión, mostrar el dashboard según el rol
    switch ($_SESSION['usuario_rol']) {
        case 'administrador':
            require_once 'views/dashboard_admin.php';
            break;
        case 'profesor':
            require_once 'views/dashboard_profesor.php';
            break;
        case 'estudiante':
            require_once 'views/dashboard_estudiante.php';
            break;
        default:
            require_once 'views/bienvenida.php';
    }
}

// Incluir el footer
require_once 'views/partials/footer.php';
?>