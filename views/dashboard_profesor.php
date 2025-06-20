<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Panel del Profesor</h2>
                <p class="card-text">Bienvenido/a, <?php echo $_SESSION['usuario_nombre']; ?>. Desde aquí puedes gestionar tus materiales educativos y evaluaciones.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-book me-2"></i>Mis Materiales</h4>
            </div>
            <div class="card-body">
                <p>Gestiona los materiales educativos para tus estudiantes.</p>
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/materiales.php?accion=crear" class="btn btn-success mb-2">
                        <i class="fas fa-plus-circle me-1"></i>Crear Nuevo Material
                    </a>
                    <a href="<?php echo BASE_URL; ?>/materiales.php?filtro=mis_materiales" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i>Ver Mis Materiales
                    </a>
                </div>
                
                <hr>
                
                <h5 class="mt-3">Materiales Recientes</h5>
                <ul class="list-group">
                    <?php
                    // Obtener materiales recientes del profesor
                    $materiales = obtenerRegistros(
                        "SELECT * FROM materiales WHERE id_usuario = ? ORDER BY fecha_creacion DESC LIMIT 3",
                        [$_SESSION['usuario_id']]
                    );
                    
                    if (count($materiales) > 0) {
                        foreach ($materiales as $material) {
                            echo '<li class="list-group-item">
                                <a href="' . BASE_URL . '/materiales.php?id=' . $material['id'] . '">' . 
                                $material['titulo'] . '</a>
                                <small class="text-muted d-block">Creado: ' . date('d/m/Y', strtotime($material['fecha_creacion'])) . '</small>
                            </li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">No has creado materiales aún</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Mis Evaluaciones</h4>
            </div>
            <div class="card-body">
                <p>Crea y gestiona evaluaciones para tus estudiantes.</p>
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/evaluaciones.php?accion=crear" class="btn btn-success mb-2">
                        <i class="fas fa-plus-circle me-1"></i>Crear Nueva Evaluación
                    </a>
                    <a href="<?php echo BASE_URL; ?>/evaluaciones.php" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i>Ver Todas las Evaluaciones
                    </a>
                </div>
                
                <hr>
                
                <h5 class="mt-3">Evaluaciones Recientes</h5>
                <ul class="list-group">
                    <?php
                    // Obtener evaluaciones recientes del profesor
                    $evaluaciones = obtenerRegistros(
                        "SELECT e.*, (SELECT COUNT(*) FROM preguntas WHERE id_evaluacion = e.id) AS num_preguntas 
                         FROM evaluaciones e WHERE id_profesor = ? ORDER BY fecha_creacion DESC LIMIT 3",
                        [$_SESSION['usuario_id']]
                    );
                    
                    if (count($evaluaciones) > 0) {
                        foreach ($evaluaciones as $evaluacion) {
                            echo '<li class="list-group-item">
                                <a href="' . BASE_URL . '/evaluaciones.php?id=' . $evaluacion['id'] . '">' . 
                                $evaluacion['titulo'] . '</a>
                                <span class="badge bg-info float-end">' . $evaluacion['num_preguntas'] . ' preguntas</span>
                                <small class="text-muted d-block">Creada: ' . date('d/m/Y', strtotime($evaluacion['fecha_creacion'])) . '</small>
                            </li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">No has creado evaluaciones aún</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-comments me-2"></i>Foros de Discusión</h4>
            </div>
            <div class="card-body">
                <p>Participa en los foros de discusión con tus estudiantes.</p>
                <div class="d-grid gap-2 col-md-4 mx-auto">
                    <a href="<?php echo BASE_URL; ?>/foros.php?accion=crear" class="btn btn-success mb-2">
                        <i class="fas fa-plus-circle me-1"></i>Crear Nuevo Tema
                    </a>
                    <a href="<?php echo BASE_URL; ?>/foros.php" class="btn btn-primary">
                        <i class="fas fa-comments me-1"></i>Ver Todos los Foros
                    </a>
                </div>
                
                <hr>
                
                <h5 class="mt-3">Temas Recientes</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tema</th>
                                <th>Creado por</th>
                                <th>Fecha</th>
                                <th>Comentarios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Obtener temas recientes
                            $temas = obtenerRegistros(
                                "SELECT t.*, u.nombre, u.apellido, 
                                 (SELECT COUNT(*) FROM foros_comentarios WHERE id_tema = t.id) AS num_comentarios 
                                 FROM foros_temas t 
                                 INNER JOIN usuarios u ON t.id_usuario = u.id 
                                 ORDER BY t.fecha_creacion DESC LIMIT 5"
                            );
                            
                            if (count($temas) > 0) {
                                foreach ($temas as $tema) {
                                    echo '<tr>
                                        <td><a href="' . BASE_URL . '/foros.php?id=' . $tema['id'] . '">' . $tema['titulo'] . '</a></td>
                                        <td>' . $tema['nombre'] . ' ' . $tema['apellido'] . '</td>
                                        <td>' . date('d/m/Y', strtotime($tema['fecha_creacion'])) . '</td>
                                        <td><span class="badge bg-secondary">' . $tema['num_comentarios'] . '</span></td>
                                        <td>
                                            <a href="' . BASE_URL . '/foros.php?id=' . $tema['id'] . '" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center">No hay temas recientes</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>