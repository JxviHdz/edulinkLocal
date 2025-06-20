<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/evaluaciones.php">Evaluaciones</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo $evaluacion['titulo']; ?></li>
  </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><?php echo $evaluacion['titulo']; ?></h3>
            <span class="badge bg-light text-dark"><?php echo count($evaluacion['preguntas']); ?> preguntas</span>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <h4>Descripción:</h4>
            <p class="card-text"><?php echo nl2br($evaluacion['descripcion']); ?></p>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="text-muted">
                    <i class="fas fa-user me-1"></i>Creada por: <?php echo $evaluacion['nombre'] . ' ' . $evaluacion['apellido']; ?>
                </span>
                <br>
                <span class="text-muted">
                    <i class="fas fa-calendar me-1"></i>Fecha: <?php echo date('d/m/Y', strtotime($evaluacion['fecha_creacion'])); ?>
                </span>
            </div>
            
            <?php if (empty($evaluacion['preguntas'])): ?>
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i>Esta evaluación no tiene preguntas
            </div>
            <?php else: ?>
            <div>
                <?php if ($evaluacion['id_profesor'] === $_SESSION['usuario_id'] || $_SESSION['usuario_rol'] === 'administrador'): ?>
                <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=agregar_preguntas&id=<?php echo $evaluacion['id']; ?>" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i>Agregar Preguntas
                </a>
                <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/realizar_evaluacion.php?id=<?php echo $evaluacion['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Realizar Evaluación
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($evaluacion['preguntas'])): ?>
        <h4 class="mt-4 mb-3"><i class="fas fa-question-circle me-2"></i>Preguntas:</h4>
        
        <div class="accordion" id="accordionPreguntas">
            <?php foreach ($evaluacion['preguntas'] as $index => $pregunta): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo $pregunta['id']; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $pregunta['id']; ?>">
                        <span class="me-2">Pregunta <?php echo $index + 1; ?>:</span> 
                        <?php echo substr($pregunta['pregunta'], 0, 100); ?>
                        <?php if (strlen($pregunta['pregunta']) > 100): ?>...<?php endif; ?>
                    </button>
                </h2>
                <div id="collapse<?php echo $pregunta['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $pregunta['id']; ?>">
                    <div class="accordion-body">
                        <p><?php echo $pregunta['pregunta']; ?></p>
                        <ul class="list-group">
                            <li class="list-group-item <?php echo $pregunta['respuesta_correcta'] === 'a' ? 'list-group-item-success' : ''; ?>">
                                <strong>A:</strong> <?php echo $pregunta['opcion_a']; ?>
                                <?php if ($pregunta['respuesta_correcta'] === 'a'): ?>
                                <span class="badge bg-success float-end"><i class="fas fa-check"></i> Correcta</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item <?php echo $pregunta['respuesta_correcta'] === 'b' ? 'list-group-item-success' : ''; ?>">
                                <strong>B:</strong> <?php echo $pregunta['opcion_b']; ?>
                                <?php if ($pregunta['respuesta_correcta'] === 'b'): ?>
                                <span class="badge bg-success float-end"><i class="fas fa-check"></i> Correcta</span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item <?php echo $pregunta['respuesta_correcta'] === 'c' ? 'list-group-item-success' : ''; ?>">
                                <strong>C:</strong> <?php echo $pregunta['opcion_c']; ?>
                                <?php if ($pregunta['respuesta_correcta'] === 'c'): ?>
                                <span class="badge bg-success float-end"><i class="fas fa-check"></i> Correcta</span>
                                <?php endif; ?>
                            </li>
                            <?php if (!empty($pregunta['opcion_d'])): ?>
                            <li class="list-group-item <?php echo $pregunta['respuesta_correcta'] === 'd' ? 'list-group-item-success' : ''; ?>">
                                <strong>D:</strong> <?php echo $pregunta['opcion_d']; ?>
                                <?php if ($pregunta['respuesta_correcta'] === 'd'): ?>
                                <span class="badge bg-success float-end"><i class="fas fa-check"></i> Correcta</span>
                                <?php endif; ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="<?php echo BASE_URL; ?>/evaluaciones.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver a Evaluaciones
    </a>
    
    <?php if ($evaluacion['id_profesor'] === $_SESSION['usuario_id'] || $_SESSION['usuario_rol'] === 'administrador'): ?>
    <div>
        <?php if (empty($evaluacion['preguntas'])): ?>
        <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=agregar_preguntas&id=<?php echo $evaluacion['id']; ?>" class="btn btn-success">
            <i class="fas fa-plus me-1"></i>Agregar Preguntas
        </a>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=agregar_preguntas&id=<?php echo $evaluacion['id']; ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit me-1"></i>Editar Preguntas
        </a>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarEvaluacionModal">
            <i class="fas fa-trash-alt me-1"></i>Eliminar
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Eliminar Evaluación -->
<div class="modal fade" id="eliminarEvaluacionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta evaluación y todas sus preguntas? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=eliminar&id=<?php echo $evaluacion['id']; ?>&csrf_token=<?php echo generarTokenCSRF(); ?>" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>