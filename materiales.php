<?php
/**
 * Página de materiales educativos
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar si el usuario está autenticado
if (!estaAutenticado()) {
    setMensaje('warning', 'Debe iniciar sesión para acceder a esta página');
    redireccionar('login.php');
}

// Procesar acciones
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? null;
$error = '';

// Si se solicita ver un material específico
if ($id && !$accion) {
    $material = obtenerMaterial($id);
    
    if (!$material) {
        setMensaje('danger', 'Material no encontrado');
        redireccionar('materiales.php');
    }
}

// Procesar creación de material
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'crear') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $titulo = limpiarDatos($_POST['titulo'] ?? '');
        $descripcion = limpiarDatos($_POST['descripcion'] ?? '');
        
        // Validar datos
        if (empty($titulo) || empty($descripcion) || empty($_FILES['archivo']['name'])) {
            $error = 'Todos los campos son obligatorios';
        } else {
            // Intentar crear el material
            $resultado = crearMaterial(
                $titulo, 
                $descripcion, 
                $_FILES['archivo'], 
                $_SESSION['usuario_id']
            );
            
            if ($resultado['exito']) {
                // Redirigir con mensaje de éxito
                setMensaje('success', $resultado['mensaje']);
                redireccionar('materiales.php');
            } else {
                $error = $resultado['mensaje'];
            }
        }
    }
}

// Incluir el header
require_once 'views/partials/header.php';

// Mostrar vista según la acción
if ($accion === 'crear') {
    if (!esProfesorOAdmin()) {
        setMensaje('danger', 'No tienes permisos para crear materiales');
        redireccionar('materiales.php');
    }
    
    require_once 'views/materiales/crear.php';
} elseif ($id && !$accion) {
    require_once 'views/materiales/ver.php';
} else {
    // Mostrar listado de materiales
    $filtro = $_GET['filtro'] ?? '';
    
    if ($filtro === 'mis_materiales') {
        $materiales = obtenerRegistros(
            "SELECT m.*, u.nombre, u.apellido 
             FROM materiales m
             INNER JOIN usuarios u ON m.id_usuario = u.id 
             WHERE m.id_usuario = ? 
             ORDER BY m.fecha_creacion DESC",
            [$_SESSION['usuario_id']]
        );
    } else {
        $materiales = obtenerMateriales();
    }
    
    require_once 'views/materiales/listar.php';
}

// Incluir el footer
require_once 'views/partials/footer.php';
?>