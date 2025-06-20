<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><i class="fas fa-clipboard-check me-2"></i><?php echo $evaluacion['titulo']; ?></h3>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <p class="card-text"><?php echo nl2br($evaluacion['descripcion']); ?></p>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>La evaluación consta de <?php echo count($evaluacion['preguntas']); ?> preguntas. Responde todas para obtener tu resultado.
            </div>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form action="<?php echo BASE_URL; ?>/realizar_evaluacion.php?id=<?php echo $id_evaluacion; ?>" method="post" id="form-evaluacion" data-validate="true">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            
            <?php foreach ($evaluacion['preguntas'] as $index => $pregunta): ?>
            <div class="card mb-4 pregunta">
                <div class="card-header">
                    <h5 class="mb-0">Pregunta <?php echo $index + 1; ?></h5>
                </div>
                <div class="card-body">
                    <p class="mb-3"><?php echo $pregunta['pregunta']; ?></p>
                    
                    <div class="opciones">
                        <div class="form-check opcion mb-2">
                            <input class="form-check-input" type="radio" name="respuestas[<?php echo $pregunta['id']; ?>]" 
                                   id="opcion_a_<?php echo $pregunta['id']; ?>" value="a" required>
                            <label class="form-check-label" for="opcion_a_<?php echo $pregunta['id']; ?>">
                                A) <?php echo $pregunta['opcion_a']; ?>
                            </label>
                        </div>
                        
                        <div class="form-check opcion mb-2">
                            <input class="form-check-input" type="radio" name="respuestas[<?php echo $pregunta['id']; ?>]" 
                                   id="opcion_b_<?php echo $pregunta['id']; ?>" value="b" required>
                            <label class="form-check-label" for="opcion_b_<?php echo $pregunta['id']; ?>">
                                B) <?php echo $pregunta['opcion_b']; ?>
                            </label>
                        </div>
                        
                        <div class="form-check opcion mb-2">
                            <input class="form-check-input" type="radio" name="respuestas[<?php echo $pregunta['id']; ?>]" 
                                   id="opcion_c_<?php echo $pregunta['id']; ?>" value="c" required>
                            <label class="form-check-label" for="opcion_c_<?php echo $pregunta['id']; ?>">
                                C) <?php echo $pregunta['opcion_c']; ?>
                            </label>
                        </div>
                        
                        <?php if (!empty($pregunta['opcion_d'])): ?>
                        <div class="form-check opcion">
                            <input class="form-check-input" type="radio" name="respuestas[<?php echo $pregunta['id']; ?>]" 
                                   id="opcion_d_<?php echo $pregunta['id']; ?>" value="d" required>
                            <label class="form-check-label" for="opcion_d_<?php echo $pregunta['id']; ?>">
                                D) <?php echo $pregunta['opcion_d']; ?>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-1"></i>Enviar Respuestas
                </button>
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>