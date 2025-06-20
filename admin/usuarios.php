<?php
/**
 * Página de gestión de usuarios (solo para administradores)
 */

// Incluir configuración y funciones
require_once '../includes/config.php';
require_once '../includes/funciones.php';

// Verificar si el usuario está autenticado y es administrador
if (!estaAutenticado() || $_SESSION['usuario_rol'] !== 'administrador') {
    setMensaje('warning', 'No tienes permisos para acceder a esta página');
    redireccionar('../index.php');
}

// Procesar acciones
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? null;
$error = '';

// Procesar creación de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'crear') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $nombre = limpiarDatos($_POST['nombre'] ?? '');
        $apellido = limpiarDatos($_POST['apellido'] ?? '');
        $email = limpiarDatos($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? '';
        
        // Validar datos
        if (empty($nombre) || empty($apellido) || empty($email) || empty($password) || empty($rol)) {
            $error = 'Todos los campos son obligatorios';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido';
        } elseif (strlen($password) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres';
        } elseif (!in_array($rol, ['administrador', 'profesor', 'estudiante'])) {
            $error = 'El rol seleccionado no es válido';
        } else {
            // Intentar registrar al usuario
            $resultado = registrarUsuario($nombre, $apellido, $email, $password, $rol);
            
            if ($resultado['exito']) {
                // Redirigir a usuarios con mensaje de éxito
                setMensaje('success', 'Usuario creado correctamente');
                redireccionar('admin/usuarios.php');
            } else {
                $error = $resultado['mensaje'];
            }
        }
    }
}

// Procesar cambio de rol
if ($accion === 'cambiar_rol' && $id) {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_GET['csrf_token'] ?? '')) {
        setMensaje('danger', 'Error de seguridad. Por favor, intente nuevamente.');
        redireccionar('admin/usuarios.php');
    } else {
        $nuevo_rol = $_GET['rol'] ?? '';
        
        if (!in_array($nuevo_rol, ['administrador', 'profesor', 'estudiante'])) {
            setMensaje('danger', 'El rol seleccionado no es válido');
            redireccionar('admin/usuarios.php');
        }
        
        // Cambiar rol
        $conexion = conectarDB();
        $stmt = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
        
        if (!$stmt) {
            setMensaje('danger', 'Error en la consulta: ' . $conexion->error);
            redireccionar('admin/usuarios.php');
        }
        
        $stmt->bind_param("si", $nuevo_rol, $id);
        $resultado = $stmt->execute();
        
        $stmt->close();
        $conexion->close();
        
        if ($resultado) {
            setMensaje('success', 'Rol actualizado correctamente');
        } else {
            setMensaje('danger', 'Error al actualizar el rol');
        }
        
        redireccionar('admin/usuarios.php');
    }
}

// Procesar eliminación de usuario
if ($accion === 'eliminar' && $id) {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_GET['csrf_token'] ?? '')) {
        setMensaje('danger', 'Error de seguridad. Por favor, intente nuevamente.');
        redireccionar('admin/usuarios.php');
    } else {
        // No permitir eliminar el propio usuario
        if ((int)$id === (int)$_SESSION['usuario_id']) {
            setMensaje('danger', 'No puedes eliminar tu propia cuenta');
            redireccionar('admin/usuarios.php');
        }
        
        // Eliminar usuario
        $conexion = conectarDB();
        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
        
        if (!$stmt) {
            setMensaje('danger', 'Error en la consulta: ' . $conexion->error);
            redireccionar('admin/usuarios.php');
        }
        
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        
        $stmt->close();
        $conexion->close();
        
        if ($resultado) {
            setMensaje('success', 'Usuario eliminado correctamente');
        } else {
            setMensaje('danger', 'Error al eliminar el usuario');
        }
        
        redireccionar('admin/usuarios.php');
    }
}

// Obtener usuarios
$usuarios = obtenerRegistros(
    "SELECT * FROM usuarios ORDER BY rol, nombre, apellido"
);

// Incluir el header
if (!defined('BASE_URL')) {
    define('BASE_URL', 'edulink/');
}
require_once '../views/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
    
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
        <i class="fas fa-user-plus me-1"></i>Nuevo Usuario
    </button>
</div>

<?php if ($error): ?>
<div class="alert alert-danger">
    <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="card shadow">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id']; ?></td>
                    <td><?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></td>
                    <td><?php echo $usuario['email']; ?></td>
                    <td>
                        <span class="badge <?php 
                            echo $usuario['rol'] === 'administrador' ? 'bg-danger' : 
                                 ($usuario['rol'] === 'profesor' ? 'bg-primary' : 'bg-success'); 
                        ?>">
                            <?php echo ucfirst($usuario['rol']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                Cambiar Rol
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item <?php echo $usuario['rol'] === 'administrador' ? 'active' : ''; ?>" 
                                       href="<?php echo BASE_URL; ?>admin/usuarios.php?accion=cambiar_rol&id=<?php echo $usuario['id']; ?>&rol=administrador&csrf_token=<?php echo generarTokenCSRF(); ?>">
                                        Administrador
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?php echo $usuario['rol'] === 'profesor' ? 'active' : ''; ?>" 
                                       href="<?php echo BASE_URL; ?>admin/usuarios.php?accion=cambiar_rol&id=<?php echo $usuario['id']; ?>&rol=profesor&csrf_token=<?php echo generarTokenCSRF(); ?>">
                                        Profesor
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?php echo $usuario['rol'] === 'estudiante' ? 'active' : ''; ?>" 
                                       href="<?php echo BASE_URL; ?>admin/usuarios.php?accion=cambiar_rol&id=<?php echo $usuario['id']; ?>&rol=estudiante&csrf_token=<?php echo generarTokenCSRF(); ?>">
                                        Estudiante
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <?php if ((int)$usuario['id'] !== (int)$_SESSION['usuario_id']): ?>
                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarModal<?php echo $usuario['id']; ?>">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                        
                        <!-- Modal Eliminar Usuario -->
                        <div class="modal fade" id="eliminarModal<?php echo $usuario['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Confirmar eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Estás seguro de que deseas eliminar al usuario "<?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?>"? Esta acción no se puede deshacer.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <a href="<?php echo BASE_URL; ?>admin/usuarios.php?accion=eliminar&id=<?php echo $usuario['id']; ?>&csrf_token=<?php echo generarTokenCSRF(); ?>" class="btn btn-danger">
                                            <i class="fas fa-trash-alt me-1"></i>Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo BASE_URL; ?>admin/usuarios.php?accion=crear" method="post" data-validate="true">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" id="apellido" name="apellido" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select id="rol" name="rol" class="form-control" required>
                            <option value="">Seleccionar rol</option>
                            <option value="estudiante">Estudiante</option>
                            <option value="profesor">Profesor</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="/edulink/index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver al Inicio
    </a>
</div>

<?php
// Incluir el footer
if (!defined('BASE_URL')) {
    define('BASE_URL', 'edulink/');
}
require_once '../views/partials/footer.php';

?>