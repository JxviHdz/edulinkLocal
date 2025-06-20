<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Crear Nuevo Tema</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form action="<?php echo BASE_URL; ?>/foros.php?accion=crear" method="post" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    
                    <div class="form-group mb-3">
                        <label for="titulo" class="form-label">Título del Tema</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="contenido" class="form-label">Contenido</label>
                        <textarea id="contenido" name="contenido" class="form-control" rows="8" required
                                  data-max-length="2000" data-counter-id="contador-contenido"></textarea>
                        <small id="contador-contenido" class="text-muted">0/2000</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="normas" required>
                            <label class="form-check-label" for="normas">
                                He leído y acepto las <a href="#" data-bs-toggle="modal" data-bs-target="#normasModal">normas del foro</a>
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Publicar Tema
                        </button>
                        <a href="<?php echo BASE_URL; ?>/foros.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Normas del Foro -->
<div class="modal fade" id="normasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Normas del Foro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Para mantener un entorno educativo respetuoso, todos los participantes deben seguir estas normas:</p>
                
                <ol>
                    <li>Ser respetuoso con todos los usuarios.</li>
                    <li>No publicar contenido ofensivo, discriminatorio o inapropiado.</li>
                    <li>No compartir información personal de otros sin su consentimiento.</li>
                    <li>Mantener las discusiones relacionadas con temas educativos.</li>
                    <li>No hacer spam ni publicidad no autorizada.</li>
                    <li>Respetar los derechos de autor y propiedad intelectual.</li>
                </ol>
                
                <p>El incumplimiento de estas normas puede resultar en la eliminación del contenido o la suspensión de la cuenta.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>