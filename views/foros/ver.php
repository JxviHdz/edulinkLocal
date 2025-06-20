<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/foros.php">Foros</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo $tema['titulo']; ?></li>
  </ol>
</nav>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0"><?php echo $tema['titulo']; ?></h3>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div class="flex-shrink-0">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="ms-3">
                <h5 class="mb-0"><?php echo $tema['nombre'] . ' ' . $tema['apellido']; ?></h5>
                <small class="text-muted">
                    <i class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y H:i', strtotime($tema['fecha_creacion'])); ?>
                </small>
            </div>
        </div>
        
        <div class="tema-contenido p-3 bg-light rounded mb-4">
            <?php echo nl2br($tema['contenido']); ?>
        </div>
    </div>
</div>

<h4 class="mb-3"><i class="fas fa-comments me-2"></i>Comentarios (<?php echo count($tema['comentarios']); ?>)</h4>

<div class="comentarios mb-4">
    <?php if (empty($tema['comentarios'])): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-1"></i>No hay comentarios aún. Sé el primero en comentar.
    </div>
    <?php else: ?>
    <?php foreach ($tema['comentarios'] as $comentario): ?>
    <div class="card mb-3">
        <div class="card-body foro-comentario foro-comentario-<?php echo $comentario['rol']; ?>">
            <div class="d-flex justify-content-between align-items-top mb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; background-color: 
                             <?php 
                             echo $comentario['rol'] === 'administrador' ? '#EF4444' : 
                                  ($comentario['rol'] === 'profesor' ? '#2563EB' : '#10B981'); 
                             ?>; color: white;">
                            <i class="fas <?php 
                               echo $comentario['rol'] === 'administrador' ? 'fa-user-shield' : 
                                    ($comentario['rol'] === 'profesor' ? 'fa-chalkboard-teacher' : 'fa-user-graduate'); 
                               ?>"></i>
                        </div>
                    </div>
                    <div class="ms-2">
                        <div class="fw-bold">
                            <?php echo $comentario['nombre'] . ' ' . $comentario['apellido']; ?>
                            <span class="badge bg-<?php 
                                  echo $comentario['rol'] === 'administrador' ? 'danger' : 
                                       ($comentario['rol'] === 'profesor' ? 'primary' : 'success'); 
                                  ?> ms-1">
                                <?php echo ucfirst($comentario['rol']); ?>
                            </span>
                        </div>
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y H:i', strtotime($comentario['fecha_creacion'])); ?>
                        </small>
                    </div>
                </div>
                <?php if ($comentario['id_usuario'] !== $_SESSION['usuario_id']): ?>
                <div>
                    <a href="<?php echo BASE_URL; ?>/foros.php?accion=reportar&id_comentario=<?php echo $comentario['id']; ?>&id_tema=<?php echo $tema['id']; ?>&csrf_token=<?php echo generarTokenCSRF(); ?>" class="btn btn-sm btn-outline-danger btn-reportar">
                        <i class="fas fa-flag me-1"></i>Reportar
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="comentario-contenido">
                <?php echo nl2br($comentario['contenido']); ?>
            </div>
            
            <?php if ($comentario['reportado']): ?>
            <div class="mt-2">
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle me-1"></i>Este comentario ha sido reportado
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-reply me-2"></i>Responder</h4>
    </div>
    <div class="card-body">
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form action="<?php echo BASE_URL; ?>/foros.php?accion=comentar" method="post" data-validate="true">
            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
            <input type="hidden" name="id_tema" value="<?php echo $tema['id']; ?>">
            
            <div class="form-group mb-3">
                <label for="contenido" class="form-label">Tu comentario</label>
                <textarea id="contenido" name="contenido" class="form-control" rows="5" required
                          data-max-length="1000" data-counter-id="contador-contenido"></textarea>
                <small id="contador-contenido" class="text-muted">0/1000</small>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>Publicar Comentario
                </button>
            </div>
        </form>
    </div>
</div>

<div class="mt-4">
    <a href="<?php echo BASE_URL; ?>/foros.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver a Foros
    </a>
</div>