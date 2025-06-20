<?php
/**
 * Página de evaluaciones
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar si el usuario está autenticado y es profesor o administrador
if (!estaAutenticado() || !esProfesorOAdmin()) {
    setMensaje('warning', 'No tienes permisos para acceder a esta página');
    redireccionar('index.php');
}

// Procesar acciones
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? null;
$error = '';

// Si se solicita ver una evaluación específica
if ($id && !$accion) {
    $evaluacion = obtenerEvaluacionConPreguntas($id);
    
    if (!$evaluacion) {
        setMensaje('danger', 'Evaluación no encontrada');
        redireccionar('evaluaciones.php');
    }
}

// Procesar creación de evaluación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'crear') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $titulo = limpiarDatos($_POST['titulo'] ?? '');
        $descripcion = limpiarDatos($_POST['descripcion'] ?? '');
        
        // Validar datos
        if (empty($titulo) || empty($descripcion)) {
            $error = 'Todos los campos son obligatorios';
        } else {
            // Intentar crear la evaluación
            $resultado = crearEvaluacion(
                $titulo, 
                $descripcion, 
                $_SESSION['usuario_id']
            );
            
            if ($resultado['exito']) {
                // Redirigir a agregar preguntas
                setMensaje('success', $resultado['mensaje']);
                redireccionar('evaluaciones.php?accion=agregar_preguntas&id=' . $resultado['id']);
            } else {
                $error = $resultado['mensaje'];
            }
        }
    }
}

// Procesar agregación de preguntas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'agregar_preguntas') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        $id_evaluacion = $_POST['id_evaluacion'] ?? '';
        $pregunta = limpiarDatos($_POST['pregunta'] ?? '');
        $opcion_a = limpiarDatos($_POST['opcion_a'] ?? '');
        $opcion_b = limpiarDatos($_POST['opcion_b'] ?? '');
        $opcion_c = limpiarDatos($_POST['opcion_c'] ?? '');
        $opcion_d = limpiarDatos($_POST['opcion_d'] ?? '');
        $respuesta_correcta = $_POST['respuesta_correcta'] ?? '';
        
        // Validar datos
        if (empty($pregunta) || empty($opcion_a) || empty($opcion_b) || empty($opcion_c) || empty($respuesta_correcta)) {
            $error = 'Todos los campos son obligatorios, excepto la Opción D';
        } elseif (!in_array($respuesta_correcta, ['a', 'b', 'c', 'd'])) {
            $error = 'La respuesta correcta debe ser A, B, C o D';
        } else {
            // Intentar agregar la pregunta
            $resultado = agregarPregunta(
                $id_evaluacion,
                $pregunta,
                $opcion_a,
                $opcion_b,
                $opcion_c,
                $opcion_d,
                $respuesta_correcta
            );
            
            if ($resultado['exito']) {
                // Redirigir a la misma página para agregar más preguntas
                setMensaje('success', $resultado['mensaje']);
                redireccionar('evaluaciones.php?accion=agregar_preguntas&id=' . $id_evaluacion);
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
    require_once 'views/evaluaciones/crear.php';
} elseif ($accion === 'agregar_preguntas') {
    $id_evaluacion = $_GET['id'] ?? '';
    
    // Verificar si la evaluación existe
    $evaluacion = obtenerRegistro(
        "SELECT * FROM evaluaciones WHERE id = ?",
        [$id_evaluacion]
    );
    
    if (!$evaluacion) {
        setMensaje('danger', 'Evaluación no encontrada');
        redireccionar('evaluaciones.php');
    }
    
    // Obtener las preguntas existentes
    $preguntas = obtenerRegistros(
        "SELECT * FROM preguntas WHERE id_evaluacion = ? ORDER BY id",
        [$id_evaluacion]
    );
    
    require_once 'views/evaluaciones/agregar_preguntas.php';
} elseif ($id && !$accion) {
    require_once 'views/evaluaciones/ver.php';
} else {
    // Mostrar listado de evaluaciones
    $evaluaciones = obtenerEvaluaciones();
    require_once 'views/evaluaciones/listar.php';
}

// Incluir el footer
require_once 'views/partials/footer.php';
?>