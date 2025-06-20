<?php
/**
 * Página para ver resultado de evaluación
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar si el usuario está autenticado
if (!estaAutenticado()) {
    setMensaje('warning', 'Debe iniciar sesión para acceder a esta página');
    redireccionar('login.php');
}

// Obtener ID del resultado
$id_resultado = $_GET['id'] ?? '';

if (empty($id_resultado)) {
    setMensaje('danger', 'Resultado no especificado');
    redireccionar('index.php');
}

// Obtener resultado
$resultado_bd = obtenerRegistro(
    "SELECT r.*, e.titulo, e.descripcion 
     FROM resultados r
     INNER JOIN evaluaciones e ON r.id_evaluacion = e.id 
     WHERE r.id = ?",
    [$id_resultado]
);

// Verificar que existe y pertenece al usuario (o es profesor/admin)
if (!$resultado_bd || 
    ($resultado_bd['id_estudiante'] !== $_SESSION['usuario_id'] && 
     !esProfesorOAdmin())) {
    setMensaje('danger', 'No tienes permiso para ver este resultado');
    redireccionar('index.php');
}

// Obtener evaluación con preguntas
$evaluacion = obtenerEvaluacionConPreguntas($resultado_bd['id_evaluacion']);

if (!$evaluacion) {
    setMensaje('danger', 'Evaluación no encontrada');
    redireccionar('index.php');
}

// Obtener respuestas del estudiante (simulado - en un sistema real estarían en la BD)
// Para simplificar, generaremos respuestas simuladas
$respuestas_correctas = [];
foreach ($evaluacion['preguntas'] as $pregunta) {
    // Simular respuestas del estudiante
    // En un sistema real, estas se obtendrían de la BD
    $respuesta_aleatoria = ['a', 'b', 'c', 'd'][rand(0, 3)];
    
    // Ajustar para que coincida con el puntaje obtenido
    $correcta = rand(0, 1) == 1 && count($respuestas_correctas) < $resultado_bd['puntaje'];
    $respuesta_usuario = $correcta ? $pregunta['respuesta_correcta'] : $respuesta_aleatoria;
    
    $respuestas_correctas[$pregunta['id']] = [
        'usuario' => $respuesta_usuario,
        'correcta' => $pregunta['respuesta_correcta']
    ];
}

$resultado = [
    'id' => $resultado_bd['id'],
    'puntaje' => $resultado_bd['puntaje'],
    'total' => count($evaluacion['preguntas']),
    'respuestas' => $respuestas_correctas,
    'fecha_realizacion' => $resultado_bd['fecha_realizacion']
];

// Incluir el header
require_once 'views/partials/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
    <?php if ($_SESSION['usuario_rol'] === 'estudiante'): ?>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/mis_evaluaciones.php">Mis Evaluaciones</a></li>
    <?php else: ?>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/evaluaciones.php">Evaluaciones</a></li>
    <?php endif; ?>
    <li class="breadcrumb-item active" aria-current="page">Resultado</li>
  </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Resultado: <?php echo $evaluacion['titulo']; ?></h3>
    </div>
    <div class="card-body text-center">
        <div class="mb-4">
            <h4>Puntuación</h4>
            <div class="display-1 mb-3">
                <span class="<?php echo $resultado['puntaje'] / $resultado['total'] >= 0.6 ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $resultado['puntaje']; ?>
                </span>
                <span class="text-muted">/ <?php echo $resultado['total']; ?></span>
            </div>
            
            <div class="progress mb-3" style="height: 20px;">
                <div class="progress-bar <?php echo $resultado['puntaje'] / $resultado['total'] >= 0.6 ? 'bg-success' : 'bg-danger'; ?>" 
                     style="width: <?php echo ($resultado['puntaje'] / $resultado['total']) * 100; ?>%;">
                    <?php echo round(($resultado['puntaje'] / $resultado['total']) * 100); ?>%
                </div>
            </div>
            
            <?php if ($resultado['puntaje'] / $resultado['total'] >= 0.6): ?>
                <div class="alert alert-success">
                    <i class="fas fa-thumbs-up me-1"></i>¡Felicidades! Has aprobado esta evaluación.
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>No has alcanzado el puntaje mínimo para aprobar (60%).
                </div>
            <?php endif; ?>
            
            <p class="text-muted">
                <i class="far fa-calendar-alt me-1"></i>Fecha de realización: <?php echo date('d/m/Y H:i', strtotime($resultado['fecha_realizacion'])); ?>
            </p>
        </div>
    </div>
</div>

<h4 class="mb-3"><i class="fas fa-list me-2"></i>Revisión de respuestas</h4>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php foreach ($evaluacion['preguntas'] as $index => $pregunta): ?>
            <div class="pregunta mb-4">
                <h5>Pregunta <?php echo $index + 1; ?></h5>
                <p><?php echo $pregunta['pregunta']; ?></p>
                
                <ul class="list-group">
                    <li class="list-group-item <?php 
                            echo $resultado['respuestas'][$pregunta['id']]['usuario'] === 'a' 
                                ? ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'a' ? 'list-group-item-success' : 'list-group-item-danger') 
                                : ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'a' ? 'list-group-item-success' : ''); 
                          ?>">
                        <strong>A:</strong> <?php echo $pregunta['opcion_a']; ?>
                        
                        <?php if ($resultado['respuestas'][$pregunta['id']]['usuario'] === 'a'): ?>
                            <span class="float-end">
                                <?php if ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'a'): ?>
                                    <i class="fas fa-check text-success"></i> Tu respuesta (correcta)
                                <?php else: ?>
                                    <i class="fas fa-times text-danger"></i> Tu respuesta (incorrecta)
                                <?php endif; ?>
                            </span>
                        <?php elseif ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'a'): ?>
                            <span class="float-end text-success">
                                <i class="fas fa-check"></i> Respuesta correcta
                            </span>
                        <?php endif; ?>
                    </li>
                    
                    <li class="list-group-item <?php 
                            echo $resultado['respuestas'][$pregunta['id']]['usuario'] === 'b' 
                                ? ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'b' ? 'list-group-item-success' : 'list-group-item-danger') 
                                : ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'b' ? 'list-group-item-success' : ''); 
                          ?>">
                        <strong>B:</strong> <?php echo $pregunta['opcion_b']; ?>
                        
                        <?php if ($resultado['respuestas'][$pregunta['id']]['usuario'] === 'b'): ?>
                            <span class="float-end">
                                <?php if ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'b'): ?>
                                    <i class="fas fa-check text-success"></i> Tu respuesta (correcta)
                                <?php else: ?>
                                    <i class="fas fa-times text-danger"></i> Tu respuesta (incorrecta)
                                <?php endif; ?>
                            </span>
                        <?php elseif ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'b'): ?>
                            <span class="float-end text-success">
                                <i class="fas fa-check"></i> Respuesta correcta
                            </span>
                        <?php endif; ?>
                    </li>
                    
                    <li class="list-group-item <?php 
                            echo $resultado['respuestas'][$pregunta['id']]['usuario'] === 'c' 
                                ? ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'c' ? 'list-group-item-success' : 'list-group-item-danger') 
                                : ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'c' ? 'list-group-item-success' : ''); 
                          ?>">
                        <strong>C:</strong> <?php echo $pregunta['opcion_c']; ?>
                        
                        <?php if ($resultado['respuestas'][$pregunta['id']]['usuario'] === 'c'): ?>
                            <span class="float-end">
                                <?php if ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'c'): ?>
                                    <i class="fas fa-check text-success"></i> Tu respuesta (correcta)
                                <?php else: ?>
                                    <i class="fas fa-times text-danger"></i> Tu respuesta (incorrecta)
                                <?php endif; ?>
                            </span>
                        <?php elseif ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'c'): ?>
                            <span class="float-end text-success">
                                <i class="fas fa-check"></i> Respuesta correcta
                            </span>
                        <?php endif; ?>
                    </li>
                    
                    <?php if (!empty($pregunta['opcion_d'])): ?>
                    <li class="list-group-item <?php 
                            echo $resultado['respuestas'][$pregunta['id']]['usuario'] === 'd' 
                                ? ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'd' ? 'list-group-item-success' : 'list-group-item-danger') 
                                : ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'd' ? 'list-group-item-success' : ''); 
                          ?>">
                        <strong>D:</strong> <?php echo $pregunta['opcion_d']; ?>
                        
                        <?php if ($resultado['respuestas'][$pregunta['id']]['usuario'] === 'd'): ?>
                            <span class="float-end">
                                <?php if ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'd'): ?>
                                    <i class="fas fa-check text-success"></i> Tu respuesta (correcta)
                                <?php else: ?>
                                    <i class="fas fa-times text-danger"></i> Tu respuesta (incorrecta)
                                <?php endif; ?>
                            </span>
                        <?php elseif ($resultado['respuestas'][$pregunta['id']]['correcta'] === 'd'): ?>
                            <span class="float-end text-success">
                                <i class="fas fa-check"></i> Respuesta correcta
                            </span>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <?php if ($index < count($evaluacion['preguntas']) - 1): ?>
                <hr>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="d-flex justify-content-between">
    <?php if ($_SESSION['usuario_rol'] === 'estudiante'): ?>
    <a href="<?php echo BASE_URL; ?>/mis_evaluaciones.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Mis Evaluaciones
    </a>
    <?php else: ?>
    <a href="<?php echo BASE_URL; ?>/evaluaciones.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Evaluaciones
    </a>
    <?php endif; ?>
    
    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary">
        <i class="fas fa-home me-1"></i>Volver al Inicio
    </a>
</div>

<?php
// Incluir el footer
require_once 'views/partials/footer.php';
?>