<?php
/**
 * Página de perfil de usuario
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Verificar si el usuario está autenticado
if (!estaAutenticado()) {
    setMensaje('warning', 'Debe iniciar sesión para acceder a esta página');
    redireccionar('login.php');
}

// Obtener información del usuario
$usuario = obtenerUsuarioActual();

if (!$usuario) {
    setMensaje('danger', 'Error al obtener información del usuario');
    redireccionar('index.php');
}

$error = '';
$mensaje = '';

// Procesar actualización de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_datos'])) {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $nombre = limpiarDatos($_POST['nombre'] ?? '');
        $apellido = limpiarDatos($_POST['apellido'] ?? '');
        
        // Validar datos
        if (empty($nombre) || empty($apellido)) {
            $error = 'Nombre y apellido son obligatorios';
        } else {
            // Actualizar datos
            $conexion = conectarDB();
            $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, apellido = ? WHERE id = ?");
            
            if (!$stmt) {
                $error = 'Error en la consulta: ' . $conexion->error;
            } else {
                $stmt->bind_param("ssi", $nombre, $apellido, $_SESSION['usuario_id']);
                $resultado = $stmt->execute();
                
                $stmt->close();
                $conexion->close();
                
                if ($resultado) {
                    // Actualizar sesión
                    $_SESSION['usuario_nombre'] = $nombre . ' ' . $apellido;
                    
                    // Actualizar variable de usuario
                    $usuario['nombre'] = $nombre;
                    $usuario['apellido'] = $apellido;
                    
                    $mensaje = 'Datos actualizados correctamente';
                } else {
                    $error = 'Error al actualizar los datos';
                }
            }
        }
    }
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_password'])) {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nuevo = $_POST['password_nuevo'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';
        
        // Validar datos
        if (empty($password_actual) || empty($password_nuevo) || empty($confirmar_password)) {
            $error = 'Todos los campos son obligatorios';
        } elseif (strlen($password_nuevo) < 6) {
            $error = 'La nueva contraseña debe tener al menos 6 caracteres';
        } elseif ($password_nuevo !== $confirmar_password) {
            $error = 'Las contraseñas no coinciden';
        } else {
            // Verificar contraseña actual
            $usuario_actual = obtenerRegistro(
                "SELECT password FROM usuarios WHERE id = ?",
                [$_SESSION['usuario_id']]
            );
            
            if (!$usuario_actual || !password_verify($password_actual, $usuario_actual['password'])) {
                $error = 'La contraseña actual es incorrecta';
            } else {
                // Actualizar contraseña
                $password_hash = password_hash($password_nuevo, PASSWORD_DEFAULT);
                
                $conexion = conectarDB();
                $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                
                if (!$stmt) {
                    $error = 'Error en la consulta: ' . $conexion->error;
                } else {
                    $stmt->bind_param("si", $password_hash, $_SESSION['usuario_id']);
                    $resultado = $stmt->execute();
                    
                    $stmt->close();
                    $conexion->close();
                    
                    if ($resultado) {
                        $mensaje = 'Contraseña actualizada correctamente';
                    } else {
                        $error = 'Error al actualizar la contraseña';
                    }
                }
            }
        }
    }
}

// Incluir el header
require_once 'views/partials/header.php';
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user me-2"></i>Información de Perfil</h4>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 100px; height: 100px; font-size: 40px;">
                        <?php echo strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['apellido'], 0, 1)); ?>
                    </div>
                </div>
                
                <h4><?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></h4>
                <p class="text-muted"><?php echo $usuario['email']; ?></p>
                
                <div class="mt-3">
                    <span class="badge bg-<?php 
                        echo $usuario['rol'] === 'administrador' ? 'danger' : 
                             ($usuario['rol'] === 'profesor' ? 'primary' : 'success'); 
                    ?> p-2">
                        <i class="fas <?php 
                           echo $usuario['rol'] === 'administrador' ? 'fa-user-shield' : 
                                ($usuario['rol'] === 'profesor' ? 'fa-chalkboard-teacher' : 'fa-user-graduate'); 
                        ?> me-1"></i>
                        <?php echo ucfirst($usuario['rol']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card shadow mt-4">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h4>
            </div>
            <div class="card-body">
                <?php
                // Obtener estadísticas según el rol
                $conexion = conectarDB();
                
                if ($usuario['rol'] === 'profesor') {
                    // Materiales creados
                    $result = $conexion->query("SELECT COUNT(*) as total FROM materiales WHERE id_usuario = " . $usuario['id']);
                    $materiales = $result->fetch_assoc()['total'];
                    
                    // Evaluaciones creadas
                    $result = $conexion->query("SELECT COUNT(*) as total FROM evaluaciones WHERE id_profesor = " . $usuario['id']);
                    $evaluaciones = $result->fetch_assoc()['total'];
                    
                    // Temas creados en foros
                    $result = $conexion->query("SELECT COUNT(*) as total FROM foros_temas WHERE id_usuario = " . $usuario['id']);
                    $temas = $result->fetch_assoc()['total'];
                    
                    echo '
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-book me-1"></i>Materiales:</span>
                        <span class="badge bg-primary rounded-pill">' . $materiales . '</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-clipboard-check me-1"></i>Evaluaciones:</span>
                        <span class="badge bg-info rounded-pill">' . $evaluaciones . '</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-comments me-1"></i>Temas en foros:</span>
                        <span class="badge bg-secondary rounded-pill">' . $temas . '</span>
                    </div>';
                } elseif ($usuario['rol'] === 'estudiante') {
                    // Evaluaciones realizadas
                    $result = $conexion->query("SELECT COUNT(*) as total FROM resultados WHERE id_estudiante = " . $usuario['id']);
                    $evaluaciones = $result->fetch_assoc()['total'];
                    
                    // Promedio de puntajes
                    $result = $conexion->query("SELECT AVG(puntaje) as promedio FROM resultados WHERE id_estudiante = " . $usuario['id']);
                    $promedio = $result->fetch_assoc()['promedio'];
                    $promedio = $promedio ? number_format($promedio, 1) : 'N/A';
                    
                    // Comentarios en foros
                    $result = $conexion->query("SELECT COUNT(*) as total FROM foros_comentarios WHERE id_usuario = " . $usuario['id']);
                    $comentarios = $result->fetch_assoc()['total'];
                    
                    echo '
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-clipboard-list me-1"></i>Evaluaciones realizadas:</span>
                        <span class="badge bg-primary rounded-pill">' . $evaluaciones . '</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-star me-1"></i>Promedio:</span>
                        <span class="badge bg-info rounded-pill">' . $promedio . '</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-comment me-1"></i>Comentarios:</span>
                        <span class="badge bg-secondary rounded-pill">' . $comentarios . '</span>
                    </div>';
                } else {
                    // Usuarios totales
                    $result = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
                    $usuarios_total = $result->fetch_assoc()['total'];
                    
                    // Materiales totales
                    $result = $conexion->query("SELECT COUNT(*) as total FROM materiales");
                    $materiales = $result->fetch_assoc()['total'];
                    
                    // Evaluaciones totales
                    $result = $conexion->query("SELECT COUNT(*) as total FROM evaluaciones");
                    $evaluaciones = $result->fetch_assoc()['total'];
                    
                    echo '
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-users me-1"></i>Usuarios:</span>
                        <span class="badge bg-primary rounded-pill">' . $usuarios_total . '</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-book me-1"></i>Materiales:</span>
                        <span class="badge bg-info rounded-pill">' . $materiales . '</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-clipboard-check me-1"></i>Evaluaciones:</span>
                        <span class="badge bg-secondary rounded-pill">' . $evaluaciones . '</span>
                    </div>';
                }
                
                $conexion->close();
                ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Perfil</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($mensaje): ?>
                <div class="alert alert-success">
                    <?php echo $mensaje; ?>
                </div>
                <?php endif; ?>
                
                <form action="<?php echo BASE_URL; ?>/perfil.php" method="post" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    <input type="hidden" name="actualizar_datos" value="1">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required 
                                       value="<?php echo $usuario['nombre']; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" id="apellido" name="apellido" class="form-control" required 
                                       value="<?php echo $usuario['apellido']; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" id="email" class="form-control" value="<?php echo $usuario['email']; ?>" readonly>
                        <small class="text-muted">El correo electrónico no se puede cambiar</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <input type="text" id="rol" class="form-control" value="<?php echo ucfirst($usuario['rol']); ?>" readonly>
                        <small class="text-muted">El rol solo puede ser cambiado por un administrador</small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="fas fa-key me-2"></i>Cambiar Contraseña</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/perfil.php" method="post" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    <input type="hidden" name="cambiar_password" value="1">
                    
                    <div class="form-group mb-3">
                        <label for="password_actual" class="form-label">Contraseña Actual</label>
                        <input type="password" id="password_actual" name="password_actual" class="form-control" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_nuevo" class="form-label">Nueva Contraseña</label>
                                <input type="password" id="password_nuevo" name="password_nuevo" class="form-control" required>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirmar_password" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-1"></i>Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver al Inicio
    </a>
</div>

<?php
// Incluir el footer
require_once 'views/partials/footer.php';
?>