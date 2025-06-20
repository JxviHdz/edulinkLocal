<?php
/**
 * Página de evaluaciones para estudiantes
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar si el usuario está autenticado y es estudiante
if (!estaAutenticado() || $_SESSION['usuario_rol'] !== 'estudiante') {
    setMensaje('warning', 'No tienes permisos para acceder a esta página');
    redireccionar('index.php');
}

// Obtener resultados del estudiante
$resultados = obtenerResultadosEstudiante($_SESSION['usuario_id']);

// Obtener evaluaciones disponibles (que no ha realizado)
$evaluaciones_disponibles = obtenerRegistros(
    "SELECT e.*, u.nombre, u.apellido, 
     (SELECT COUNT(*) FROM preguntas WHERE id_evaluacion = e.id) AS num_preguntas
     FROM evaluaciones e
     INNER JOIN usuarios u ON e.id_profesor = u.id 
     WHERE e.id NOT IN (
         SELECT id_evaluacion FROM resultados WHERE id_estudiante = ?
     )
     AND (SELECT COUNT(*) FROM preguntas WHERE id_evaluacion = e.id) > 0
     ORDER BY e.fecha_creacion DESC",
    [$_SESSION['usuario_id']]
);

// Incluir el header
require_once 'views/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clipboard-check me-2"></i>Mis Evaluaciones</h2>
</div>

<div class="row">
    <!-- Evaluaciones disponibles -->
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Evaluaciones Disponibles</h4>
            </div>
            <div class="card-body">
                <?php if (empty($evaluaciones_disponibles)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>No hay evaluaciones disponibles para realizar.
                </div>
                <?php else: ?>
                <div class="list-group">
                    <?php foreach ($evaluaciones_disponibles as $evaluacion): ?>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?php echo $evaluacion['titulo']; ?></h5>
                            <p class="mb-1 text-muted">
                                <small>
                                    <i class="fas fa-user me-1"></i><?php echo $evaluacion['nombre'] . ' ' . $evaluacion['apellido']; ?>
                                </small>
                            </p>
                            <p class="mb-1">
                                <span class="badge bg-info">
                                    <i class="fas fa-question-circle me-1"></i><?php echo $evaluacion['num_preguntas']; ?> preguntas
                                </span>
                            </p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/realizar_evaluacion.php?id=<?php echo $evaluacion['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Realizar
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Evaluaciones completadas -->
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i>Evaluaciones Completadas</h4>
            </div>
            <div class="card-body">
                <?php if (empty($resultados)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>No has completado ninguna evaluación.
                </div>
                <?php else: ?>
                <div class="list-group">
                    <?php foreach ($resultados as $resultado): ?>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?php echo $resultado['titulo']; ?></h5>
                            <p class="mb-1">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y H:i', strtotime($resultado['fecha_realizacion'])); ?>
                                </small>
                            </p>
                            <div>
                                <span class="badge <?php echo ($resultado['puntaje'] / $resultado['total_preguntas'] >= 0.6) ? 'bg-success' : 'bg-danger'; ?>">
                                    <i class="fas fa-star me-1"></i>Puntaje: <?php echo $resultado['puntaje']; ?>/<?php echo $resultado['total_preguntas']; ?>
                                    (<?php echo round(($resultado['puntaje'] / $resultado['total_preguntas']) * 100); ?>%)
                                </span>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/ver_resultado.php?id=<?php echo $resultado['id']; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Ver Detalles
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver al Inicio
    </a>
</div>

<?php
// Incluir el footer
require_once 'views/partials/footer.php';
?>