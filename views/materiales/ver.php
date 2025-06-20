<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/materiales.php">Materiales</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo $material['titulo']; ?></li>
  </ol>
</nav>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><?php echo $material['titulo']; ?></h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h4>Descripción:</h4>
                    <p class="card-text"><?php echo nl2br($material['descripcion']); ?></p>
                </div>
                
                <h4>Contenido:</h4>
                <div class="mt-3 text-center">
                    <?php if (strpos($material['tipo_archivo'], 'image/') === 0): ?>
                        <img src="<?php echo UPLOADS_URL . $material['archivo']; ?>" alt="<?php echo $material['titulo']; ?>" class="img-fluid rounded">
                    <?php elseif ($material['tipo_archivo'] === 'application/pdf'): ?>
                        <div class="ratio ratio-16x9">
                            <iframe src="<?php echo UPLOADS_URL . $material['archivo']; ?>" allowfullscreen></iframe>
                        </div>
                        <a href="<?php echo UPLOADS_URL . $material['archivo']; ?>" class="btn btn-primary mt-3" download>
                            <i class="fas fa-download me-1"></i>Descargar PDF
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-file me-2"></i>Archivo disponible para descargar
                        </div>
                        <a href="<?php echo UPLOADS_URL . $material['archivo']; ?>" class="btn btn-primary mt-2" download>
                            <i class="fas fa-download me-1"></i>Descargar Archivo
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>Compartido por: <?php echo $material['nombre'] . ' ' . $material['apellido']; ?>
                        </small>
                    </div>
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>Publicado: <?php echo date('d/m/Y', strtotime($material['fecha_creacion'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="<?php echo BASE_URL; ?>/materiales.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver a Materiales
            </a>
            
            <?php if ($material['id_usuario'] == $_SESSION['usuario_id'] || $_SESSION['usuario_rol'] === 'administrador'): ?>
            <div>
                <a href="<?php echo BASE_URL; ?>/materiales.php?accion=editar&id=<?php echo $material['id']; ?>" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarMaterialModal">
                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h4>
            </div>
            <div class="card-body">
                <p>
                    <strong><i class="fas fa-user me-1"></i>Autor:</strong><br>
                    <?php echo $material['nombre'] . ' ' . $material['apellido']; ?>
                </p>
                <p>
                    <strong><i class="fas fa-calendar-alt me-1"></i>Fecha de publicación:</strong><br>
                    <?php echo date('d/m/Y H:i', strtotime($material['fecha_creacion'])); ?>
                </p>
                <p>
                    <strong><i class="fas fa-file me-1"></i>Tipo de archivo:</strong><br>
                    <?php 
                    if (strpos($material['tipo_archivo'], 'image/') === 0) {
                        echo 'Imagen (' . str_replace('image/', '', $material['tipo_archivo']) . ')';
                    } elseif ($material['tipo_archivo'] === 'application/pdf') {
                        echo 'Documento PDF';
                    } else {
                        echo 'Archivo ' . $material['tipo_archivo'];
                    }
                    ?>
                </p>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-book me-2"></i>Materiales Relacionados</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    // Obtener materiales relacionados (del mismo autor o con palabras clave similares)
                    $materiales_relacionados = obtenerRegistros(
                        "SELECT m.*, u.nombre, u.apellido 
                         FROM materiales m 
                         INNER JOIN usuarios u ON m.id_usuario = u.id 
                         WHERE m.id != ? AND (
                             m.id_usuario = ? OR 
                             m.titulo LIKE ? OR 
                             m.descripcion LIKE ?
                         ) 
                         ORDER BY m.fecha_creacion DESC 
                         LIMIT 3",
                        [
                            $material['id'], 
                            $material['id_usuario'],
                            '%' . substr($material['titulo'], 0, 5) . '%',
                            '%' . substr($material['descripcion'], 0, 5) . '%'
                        ]
                    );
                    
                    if (count($materiales_relacionados) > 0) {
                        foreach ($materiales_relacionados as $relacionado) {
                            echo '<li class="list-group-item">
                                <a href="' . BASE_URL . '/materiales.php?id=' . $relacionado['id'] . '">
                                    ' . $relacionado['titulo'] . '
                                </a>
                                <small class="text-muted d-block">
                                    Por: ' . $relacionado['nombre'] . ' ' . $relacionado['apellido'] . '
                                </small>
                            </li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">No hay materiales relacionados</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Material -->
<div class="modal fade" id="eliminarMaterialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este material? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="<?php echo BASE_URL; ?>/materiales.php?accion=eliminar&id=<?php echo $material['id']; ?>&csrf_token=<?php echo generarTokenCSRF(); ?>" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>