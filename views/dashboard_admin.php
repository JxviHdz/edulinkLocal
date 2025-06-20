<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Panel de Administración</h2>
                <p class="card-text">Bienvenido/a, <?php echo $_SESSION['usuario_nombre']; ?>. Desde aquí puedes administrar todos los aspectos de la plataforma.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Gestión de Usuarios</h5>
                <p class="card-text">Administra los usuarios registrados en la plataforma.</p>
            </div>
            <div class="card-footer">
                <a href="<?php echo BASE_URL; ?>/admin/usuarios.php" class="btn btn-primary">
                    <i class="fas fa-cog me-1"></i>Administrar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Contenido Educativo</h5>
                <p class="card-text">Gestiona materiales y recursos educativos.</p>
            </div>
            <div class="card-footer">
                <a href="<?php echo BASE_URL; ?>/materiales.php" class="btn btn-primary">
                    <i class="fas fa-folder-open me-1"></i>Ver Materiales
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Foros y Comentarios</h5>
                <p class="card-text">Revisa y modera los foros de discusión.</p>
            </div>
            <div class="card-footer">
                <a href="<?php echo BASE_URL; ?>/foros.php" class="btn btn-primary">
                    <i class="fas fa-comments me-1"></i>Ver Foros
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-flag fa-3x text-danger mb-3"></i>
                <h5 class="card-title">Reportes</h5>
                <p class="card-text">Revisa los comentarios reportados por los usuarios.</p>
            </div>
            <div class="card-footer">
                <a href="<?php echo BASE_URL; ?>/admin/reportes.php" class="btn btn-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>Ver Reportes
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Estadísticas de la Plataforma</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <?php
                    // Obtener estadísticas
                    $conexion = conectarDB();
                    
                    // Total de usuarios
                    $result = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
                    $usuarios = $result->fetch_assoc()['total'];
                    
                    // Total de materiales
                    $result = $conexion->query("SELECT COUNT(*) as total FROM materiales");
                    $materiales = $result->fetch_assoc()['total'];
                    
                    // Total de temas en foros
                    $result = $conexion->query("SELECT COUNT(*) as total FROM foros_temas");
                    $temas = $result->fetch_assoc()['total'];
                    
                    // Total de evaluaciones
                    $result = $conexion->query("SELECT COUNT(*) as total FROM evaluaciones");
                    $evaluaciones = $result->fetch_assoc()['total'];
                    
                    $conexion->close();
                    ?>
                    
                    <div class="col-md-3">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <h1 class="display-4 text-primary"><?php echo $usuarios; ?></h1>
                                <p class="text-muted">Usuarios Registrados</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <h1 class="display-4 text-success"><?php echo $materiales; ?></h1>
                                <p class="text-muted">Materiales Publicados</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <h1 class="display-4 text-info"><?php echo $temas; ?></h1>
                                <p class="text-muted">Temas en Foros</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <h1 class="display-4 text-warning"><?php echo $evaluaciones; ?></h1>
                                <p class="text-muted">Evaluaciones Creadas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>