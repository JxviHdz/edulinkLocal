<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-book me-2"></i>Materiales Educativos</h2>
    
    <?php if (esProfesorOAdmin()): ?>
    <a href="<?php echo BASE_URL; ?>/materiales.php?accion=crear" class="btn btn-success">
        <i class="fas fa-plus-circle me-1"></i>Nuevo Material
    </a>
    <?php endif; ?>
</div>

<?php if ($filtro === 'mis_materiales'): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>Mostrando solo tus materiales
    <a href="<?php echo BASE_URL; ?>/materiales.php" class="alert-link float-end">Ver todos</a>
</div>
<?php elseif (esProfesorOAdmin()): ?>
<div class="text-end mb-3">
    <a href="<?php echo BASE_URL; ?>/materiales.php?filtro=mis_materiales" class="btn btn-outline-primary">
        <i class="fas fa-filter me-1"></i>Ver solo mis materiales
    </a>
</div>
<?php endif; ?>

<?php if (empty($materiales)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>No hay materiales disponibles.
    <?php if (esProfesorOAdmin()): ?>
    <a href="<?php echo BASE_URL; ?>/materiales.php?accion=crear" class="alert-link">Crea el primer material</a>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="row">
    <?php foreach ($materiales as $material): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card material-card h-100">
            <?php if (strpos($material['tipo_archivo'], 'image/') === 0): ?>
                <img src="<?php echo UPLOADS_URL . $material['archivo']; ?>" alt="<?php echo $material['titulo']; ?>" class="material-thumbnail">
            <?php else: ?>
                <div class="card-img-top text-center py-4 bg-light">
                    <i class="far fa-file-pdf fa-5x text-danger"></i>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <h5 class="card-title"><?php echo $material['titulo']; ?></h5>
                <p class="card-text text-muted">
                    <small>
                        <i class="fas fa-user me-1"></i><?php echo $material['nombre'] . ' ' . $material['apellido']; ?>
                    </small>
                    <small class="ms-2">
                        <i class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y', strtotime($material['fecha_creacion'])); ?>
                    </small>
                </p>
                <p class="card-text">
                    <?php 
                    // Limitar la descripción a 100 caracteres
                    echo strlen($material['descripcion']) > 100 
                        ? substr($material['descripcion'], 0, 100) . '...' 
                        : $material['descripcion']; 
                    ?>
                </p>
            </div>
            <div class="card-footer d-grid">
                <a href="<?php echo BASE_URL; ?>/materiales.php?id=<?php echo $material['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-eye me-1"></i>Ver Material
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>