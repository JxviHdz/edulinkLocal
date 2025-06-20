<?php
/**
 * Página de foros de discusión
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

// Si se solicita ver un tema específico
if ($id && !$accion) {
    $tema = obtenerTemaForoConComentarios($id);
    
    if (!$tema) {
        setMensaje('danger', 'Tema no encontrado');
        redireccionar('foros.php');
    }
}

// Procesar creación de tema
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'crear') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $titulo = limpiarDatos($_POST['titulo'] ?? '');
        $contenido = limpiarDatos($_POST['contenido'] ?? '');
        
        // Validar datos
        if (empty($titulo) || empty($contenido)) {
            $error = 'Todos los campos son obligatorios';
        } else {
            // Intentar crear el tema
            $resultado = crearTemaForo(
                $titulo, 
                $contenido, 
                $_SESSION['usuario_id']
            );
            
            if ($resultado['exito']) {
                // Redirigir con mensaje de éxito
                setMensaje('success', $resultado['mensaje']);
                redireccionar('foros.php?id=' . $resultado['id']);
            } else {
                $error = $resultado['mensaje'];
            }
        }
    }
}

// Procesar creación de comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'comentar') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $id_tema = $_POST['id_tema'] ?? '';
        $contenido = limpiarDatos($_POST['contenido'] ?? '');
        
        // Validar datos
        if (empty($id_tema) || empty($contenido)) {
            $error = 'Todos los campos son obligatorios';
        } else {
            // Intentar crear el comentario
            $resultado = crearComentarioForo(
                $id_tema, 
                $contenido, 
                $_SESSION['usuario_id']
            );
            
            if ($resultado['exito']) {
                // Redirigir con mensaje de éxito
                setMensaje('success', $resultado['mensaje']);
                redireccionar('foros.php?id=' . $id_tema);
            } else {
                $error = $resultado['mensaje'];
            }
        }
    }
}

// Procesar reporte de comentario
if ($accion === 'reportar' && isset($_GET['id_comentario'])) {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_GET['csrf_token'] ?? '')) {
        setMensaje('danger', 'Error de seguridad. Por favor, intente nuevamente.');
        redireccionar('foros.php');
    } else {
        $id_comentario = $_GET['id_comentario'];
        $id_tema = $_GET['id_tema'] ?? '';
        
        // Reportar comentario
        $resultado = reportarComentario($id_comentario);
        
        if ($resultado['exito']) {
            setMensaje('success', 'Comentario reportado correctamente. Un administrador lo revisará pronto.');
        } else {
            setMensaje('danger', $resultado['mensaje']);
        }
        
        // Redirigir
        if ($id_tema) {
            redireccionar('foros.php?id=' . $id_tema);
        } else {
            redireccionar('foros.php');
        }
    }
}

// Incluir el header
require_once 'views/partials/header.php';

// Mostrar vista según la acción
if ($accion === 'crear') {
    require_once 'views/foros/crear.php';
} elseif ($id && !$accion) {
    require_once 'views/foros/ver.php';
} else {
    // Mostrar listado de temas
    $temas = obtenerTemasForo();
    require_once 'views/foros/listar.php';
}

// Incluir el footer
require_once 'views/partials/footer.php';
?>