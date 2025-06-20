<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Crear Nueva Evaluación</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form action="<?php echo BASE_URL; ?>/evaluaciones.php?accion=crear" method="post" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    
                    <div class="form-group mb-3">
                        <label for="titulo" class="form-label">Título de la Evaluación</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="5" required
                                  data-max-length="1000" data-counter-id="contador-descripcion"></textarea>
                        <small id="contador-descripcion" class="text-muted">0/1000</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>Después de crear la evaluación, podrás agregar las preguntas.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Crear Evaluación
                        </button>
                        <a href="<?php echo BASE_URL; ?>/evaluaciones.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>