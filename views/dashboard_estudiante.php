<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Mi Espacio de Aprendizaje</h2>
                <p class="card-text">Bienvenido/a, <?php echo $_SESSION['usuario_nombre']; ?>. Explora materiales, participa en foros y realiza evaluaciones para avanzar en tu aprendizaje.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-book me-2"></i>Materiales</h4>
            </div>
            <div class="card-body">
                <p>Accede a los materiales educativos compartidos por tus profesores.</p>
                <div class="d-grid">
                    <a href="<?php echo BASE_URL; ?>/materiales.php" class="btn btn-primary">
                        <i class="fas fa-book-reader me-1"></i>Ver Materiales
                    </a>
                </div>
                
                <hr>
                
                <h5 class="mt-3">Materiales Recientes</h5>
                <ul class="list-group">
                    <?php
                    // Obtener materiales recientes
                    $materiales = obtenerRegistros(
                        "SELECT m.*, u.nombre, u.apellido 
                         FROM materiales m
                         INNER JOIN usuarios u ON m.id_usuario = u.id 
                         ORDER BY m.fecha_creacion DESC LIMIT 3"
                    );
                    
                    if (count($materiales) > 0) {
                        foreach ($materiales as $material) {
                            echo '<li class="list-group-item">
                                <a href="' . BASE_URL . '/materiales.php?id=' . $material['id'] . '">' . 
                                $material['titulo'] . '</a>
                                <small class="text-muted d-block">Por: ' . $material['nombre'] . ' ' . $material['apellido'] . '</small>
                            </li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">No hay materiales disponibles</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-comments me-2"></i>Foros</h4>
            </div>
            <div class="card-body">
                <p>Participa en debates educativos y resuelve tus dudas en los foros de discusión.</p>
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>/foros.php?accion=crear" class="btn btn-success mb-2">
                        <i class="fas fa-plus-circle me-1"></i>Crear Nuevo Tema
                    </a>
                    <a href="<?php echo BASE_URL; ?>/foros.php" class="btn btn-primary">
                        <i class="fas fa-comments me-1"></i>Ver Foros
                    </a>
                </div>
                
                <hr>
                
                <h5 class="mt-3">Temas Recientes</h5>
                <ul class="list-group">
                    <?php
                    // Obtener temas recientes
                    $temas = obtenerRegistros(
                        "SELECT t.*, u.nombre, u.apellido 
                         FROM foros_temas t
                         INNER JOIN usuarios u ON t.id_usuario = u.id 
                         ORDER BY t.fecha_creacion DESC LIMIT 3"
                    );
                    
                    if (count($temas) > 0) {
                        foreach ($temas as $tema) {
                            echo '<li class="list-group-item">
                                <a href="' . BASE_URL . '/foros.php?id=' . $tema['id'] . '">' . 
                                $tema['titulo'] . '</a>
                                <small class="text-muted d-block">Por: ' . $tema['nombre'] . ' ' . $tema['apellido'] . '</small>
                            </li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">No hay temas recientes</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Evaluaciones</h4>
            </div>
            <div class="card-body">
                <p>Realiza evaluaciones para poner a prueba tus conocimientos.</p>
                <div class="d-grid">
                    <a href="<?php echo BASE_URL; ?>/mis_evaluaciones.php" class="btn btn-primary">
                        <i class="fas fa-clipboard-list me-1"></i>Mis Evaluaciones
                    </a>
                </div>
                
                <hr>
                
                <h5 class="mt-3">Evaluaciones Disponibles</h5>
                <ul class="list-group">
                    <?php
                    // Obtener evaluaciones recientes
                    $evaluaciones = obtenerRegistros(
                        "SELECT e.*, u.nombre, u.apellido 
                         FROM evaluaciones e
                         INNER JOIN usuarios u ON e.id_profesor = u.id 
                         WHERE e.id NOT IN (
                             SELECT id_evaluacion FROM resultados WHERE id_estudiante = ?
                         )
                         ORDER BY e.fecha_creacion DESC LIMIT 3",
                        [$_SESSION['usuario_id']]
                    );
                    
                    if (count($evaluaciones) > 0) {
                        foreach ($evaluaciones as $evaluacion) {
                            echo '<li class="list-group-item">
                                <a href="' . BASE_URL . '/realizar_evaluacion.php?id=' . $evaluacion['id'] . '">' . 
                                $evaluacion['titulo'] . '</a>
                                <small class="text-muted d-block">Por: ' . $evaluacion['nombre'] . ' ' . $evaluacion['apellido'] . '</small>
                            </li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">No hay evaluaciones disponibles</li>';
                    }
                    ?>
                </ul>
                
                <h5 class="mt-3">Mis Resultados Recientes</h5>
                <ul class="list-group">
                    <?php
                    // Obtener resultados recientes
                    $resultados = obtenerRegistros(
                        "SELECT r.*, e.titulo 
                         FROM resultados r
                         INNER JOIN evaluaciones e ON r.id_evaluacion = e.id 
                         WHERE r.id_estudiante = ? 
                         ORDER BY r.fecha_realizacion DESC LIMIT 3",
                        [$_SESSION['usuario_id']]
                    );
                    
                    if (count($resultados) > 0) {
                        foreach ($resultados as $resultado) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="' . BASE_URL . 'ver_resultado.php?id=' . $resultado['id'] . '">' . 
                                    $resultado['titulo'] . '</a>
                                    <small class="text-muted d-block">' . date('d/m/Y', strtotime($resultado['fecha_realizacion'])) . '</small>
                                </div>
                               
                            </li>';
                        }
                    } else {
                        echo '<li class="list-group-item text-center">No has realizado evaluaciones</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>