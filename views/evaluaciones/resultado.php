<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Resultado: <?php echo $evaluacion['titulo']; ?></h3>
    </div>
    <div class="card-body text-center">
        <div class="mb-4">
            <h4>Tu puntuación</h4>
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
                
                <div class="mt-2">
                    <?php if ($resultado['respuestas'][$pregunta['id']]['usuario'] === $resultado['respuestas'][$pregunta['id']]['correcta']): ?>
                        <div class="text-success"><i class="fas fa-check-circle me-1"></i>¡Respuesta correcta!</div>
                    <?php else: ?>
                        <div class="text-danger"><i class="fas fa-times-circle me-1"></i>Respuesta incorrecta</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($index < count($evaluacion['preguntas']) - 1): ?>
                <hr>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="<?php echo BASE_URL; ?>/mis_evaluaciones.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Mis Evaluaciones
    </a>
    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary">
        <i class="fas fa-home me-1"></i>Volver al Inicio
    </a>
</div>