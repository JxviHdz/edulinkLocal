<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/evaluaciones.php">Evaluaciones</a></li>
    <li class="breadcrumb-item active" aria-current="page">Agregar preguntas</li>
  </ol>
</nav>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h4>
            </div>
            <div class="card-body">
                <h5><?php echo $evaluacion['titulo']; ?></h5>
                <p class="text-muted"><?php echo $evaluacion['descripcion']; ?></p>
                
                <div class="mb-3">
                    <h6>Preguntas:</h6>
                    <h3 class="text-primary"><?php echo count($preguntas); ?></h3>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/evaluaciones.php?id=<?php echo $evaluacion['id']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>Ver Evaluación
                    </a>
                    
                    <?php if (count($preguntas) > 0): ?>
                    <a href="<?php echo BASE_URL; ?>/evaluaciones.php" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i>Finalizar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Agregar Pregunta</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form action="<?php echo BASE_URL; ?>/evaluaciones.php?accion=agregar_preguntas" method="post" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    <input type="hidden" name="id_evaluacion" value="<?php echo $evaluacion['id']; ?>">
                    
                    <div class="form-group mb-3">
                        <label for="pregunta" class="form-label">Pregunta</label>
                        <textarea id="pregunta" name="pregunta" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opcion_a" class="form-label">Opción A</label>
                                <input type="text" id="opcion_a" name="opcion_a" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opcion_b" class="form-label">Opción B</label>
                                <input type="text" id="opcion_b" name="opcion_b" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opcion_c" class="form-label">Opción C</label>
                                <input type="text" id="opcion_c" name="opcion_c" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opcion_d" class="form-label">Opción D (opcional)</label>
                                <input type="text" id="opcion_d" name="opcion_d" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Respuesta Correcta</label>
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="respuesta_correcta" id="respuesta_a" value="a" required>
                                <label class="form-check-label" for="respuesta_a">A</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="respuesta_correcta" id="respuesta_b" value="b" required>
                                <label class="form-check-label" for="respuesta_b">B</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="respuesta_correcta" id="respuesta_c" value="c" required>
                                <label class="form-check-label" for="respuesta_c">C</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="respuesta_correcta" id="respuesta_d" value="d" required>
                                <label class="form-check-label" for="respuesta_d">D</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Agregar Pregunta
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (!empty($preguntas)): ?>
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>Preguntas Agregadas</h4>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordionPreguntas">
                    <?php foreach ($preguntas as $index => $pregunta): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $pregunta['id']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $pregunta['id']; ?>">
                                Pregunta <?php echo $index + 1; ?>: <?php echo substr($pregunta['pregunta'], 0, 100); ?>
                                <?php if (strlen($pregunta['pregunta']) > 100): ?>...<?php endif; ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $pregunta['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $pregunta['id']; ?>">
                            <div class="accordion-body">
                                <p><?php echo $pregunta['pregunta']; ?></p>
                                <ul class="list-group mb-3">
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
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>