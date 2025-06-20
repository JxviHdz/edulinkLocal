<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-comments me-2"></i>Foros de Discusión</h2>
    
    <a href="<?php echo BASE_URL; ?>/foros.php?accion=crear" class="btn btn-success">
        <i class="fas fa-plus-circle me-1"></i>Nuevo Tema
    </a>
</div>

<?php if (empty($temas)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>No hay temas de discusión disponibles. 
    <a href="<?php echo BASE_URL; ?>/foros.php?accion=crear" class="alert-link">Crea el primer tema</a>
</div>
<?php else: ?>
<div class="card shadow">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Tema</th>
                    <th>Autor</th>
                    <th>Fecha</th>
                    <th>Comentarios</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($temas as $tema): ?>
                <tr>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/foros.php?id=<?php echo $tema['id']; ?>" class="text-decoration-none">
                            <?php echo $tema['titulo']; ?>
                        </a>
                    </td>
                    <td><?php echo $tema['nombre'] . ' ' . $tema['apellido']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($tema['fecha_creacion'])); ?></td>
                    <td><span class="badge bg-secondary"><?php echo $tema['num_comentarios']; ?></span></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/foros.php?id=<?php echo $tema['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>Ver
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>