<?php
/**
 * Página para realizar evaluaciones
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar si el usuario está autenticado y es estudiante
if (!estaAutenticado() || $_SESSION['usuario_rol'] !== 'estudiante') {
    setMensaje('warning', 'No tienes permisos para acceder a esta página');
    redireccionar('index.php');
}

// Obtener ID de evaluación
$id_evaluacion = $_GET['id'] ?? '';

if (empty($id_evaluacion)) {
    setMensaje('danger', 'Evaluación no especificada');
    redireccionar('index.php');
}

// Verificar si el estudiante ya realizó esta evaluación
$resultado_existente = obtenerRegistro(
    "SELECT * FROM resultados WHERE id_evaluacion = ? AND id_estudiante = ?",
    [$id_evaluacion, $_SESSION['usuario_id']]
);

if ($resultado_existente) {
    setMensaje('warning', 'Ya has realizado esta evaluación');
    redireccionar('ver_resultado.php?id=' . $resultado_existente['id']);
}

// Obtener la evaluación con sus preguntas
$evaluacion = obtenerEvaluacionConPreguntas($id_evaluacion);

if (!$evaluacion || empty($evaluacion['preguntas'])) {
    setMensaje('danger', 'La evaluación no existe o no tiene preguntas');
    redireccionar('index.php');
}

$error = '';
$resultado = null;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        $respuestas = $_POST['respuestas'] ?? [];
        
        // Verificar que se respondieron todas las preguntas
        if (count($respuestas) !== count($evaluacion['preguntas'])) {
            $error = 'Debes responder todas las preguntas';
        } else {
            // Calcular puntaje
            $puntaje = 0;
            $respuestas_correctas = [];
            
            foreach ($evaluacion['preguntas'] as $pregunta) {
                $id_pregunta = $pregunta['id'];
                $respuesta_usuario = $respuestas[$id_pregunta] ?? '';
                
                // Verificar si la respuesta es correcta
                if ($respuesta_usuario === $pregunta['respuesta_correcta']) {
                    $puntaje++;
                }
                
                // Guardar respuesta para mostrar resultados
                $respuestas_correctas[$id_pregunta] = [
                    'usuario' => $respuesta_usuario,
                    'correcta' => $pregunta['respuesta_correcta']
                ];
            }
            
            // Guardar el resultado
            $resultado_guardado = guardarResultadoEvaluacion(
                $id_evaluacion,
                $_SESSION['usuario_id'],
                $puntaje
            );
            
            if ($resultado_guardado['exito']) {
                $resultado = [
                    'id' => $resultado_guardado['id'],
                    'puntaje' => $puntaje,
                    'total' => count($evaluacion['preguntas']),
                    'respuestas' => $respuestas_correctas
                ];
            } else {
                $error = $resultado_guardado['mensaje'];
            }
        }
    }
}

// Incluir el header
require_once 'views/partials/header.php';

// Mostrar vista
if ($resultado) {
    require_once 'views/evaluaciones/resultado.php';
} else {
    require_once 'views/evaluaciones/realizar.php';
}

// Incluir el footer
require_once 'views/partials/footer.php';
?>