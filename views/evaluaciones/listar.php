<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clipboard-check me-2"></i>Evaluaciones</h2>
    
    <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=crear" class="btn btn-success">
        <i class="fas fa-plus-circle me-1"></i>Nueva Evaluación
    </a>
</div>

<?php if (empty($evaluaciones)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>No hay evaluaciones disponibles. 
    <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=crear" class="alert-link">Crea la primera evaluación</a>
</div>
<?php else: ?>
<div class="card shadow">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Autor</th>
                    <th>Preguntas</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evaluaciones as $evaluacion): ?>
                <tr>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/evaluaciones.php?id=<?php echo $evaluacion['id']; ?>" class="text-decoration-none">
                            <?php echo $evaluacion['titulo']; ?>
                        </a>
                    </td>
                    <td>
                        <?php 
                        // Limitar la descripción a 50 caracteres
                        echo strlen($evaluacion['descripcion']) > 50 
                            ? substr($evaluacion['descripcion'], 0, 50) . '...' 
                            : $evaluacion['descripcion']; 
                        ?>
                    </td>
                    <td><?php echo $evaluacion['nombre'] . ' ' . $evaluacion['apellido']; ?></td>
                    <td><span class="badge bg-info"><?php echo $evaluacion['num_preguntas']; ?></span></td>
                    <td><?php echo date('d/m/Y', strtotime($evaluacion['fecha_creacion'])); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/evaluaciones.php?id=<?php echo $evaluacion['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>Ver
                        </a>
                        
                        <?php if ($evaluacion['id_profesor'] === $_SESSION['usuario_id'] || $_SESSION['usuario_rol'] === 'administrador'): ?>
                        <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=agregar_preguntas&id=<?php echo $evaluacion['id']; ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i>Preguntas
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>