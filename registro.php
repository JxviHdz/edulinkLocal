<?php
/**
 * Página de registro de usuarios
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Si ya hay sesión iniciada, redirigir al inicio
if (estaAutenticado()) {
    redireccionar('index.php');
}

$error = '';
$datos = [
    'nombre' => '',
    'apellido' => '',
    'email' => ''
];

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $datos['nombre'] = limpiarDatos($_POST['nombre'] ?? '');
        $datos['apellido'] = limpiarDatos($_POST['apellido'] ?? '');
        $datos['email'] = limpiarDatos($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';
        
        // Validar datos
        if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['email']) || empty($password) || empty($confirmar_password)) {
            $error = 'Todos los campos son obligatorios';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido';
        } elseif (strlen($password) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres';
        } elseif ($password !== $confirmar_password) {
            $error = 'Las contraseñas no coinciden';
        } else {
            // Intentar registrar al usuario
            $resultado = registrarUsuario($datos['nombre'], $datos['apellido'], $datos['email'], $password, 'estudiante');
            
            if ($resultado['exito']) {
                // Redirigir al login con mensaje de éxito
                setMensaje('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
                redireccionar('login.php');
            } else {
                $error = $resultado['mensaje'];
            }
        }
    }
}

// Incluir el header
require_once 'views/partials/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Registro de Usuario</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form action="registro.php" method="post" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" id="nombre" name="nombre" class="form-control" required value="<?php echo $datos['nombre']; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" id="apellido" name="apellido" class="form-control" required value="<?php echo $datos['apellido']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" required value="<?php echo $datos['email']; ?>">
                        </div>
                        <small class="text-muted">Este correo será tu nombre de usuario</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="password" name="password" class="form-control" required>
                                </div>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terminos" required>
                            <label class="form-check-label" for="terminos">
                                Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#terminosModal">términos y condiciones</a>
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i>Registrarme
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>/login.php">Inicia Sesión</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Términos y Condiciones -->
<div class="modal fade" id="terminosModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Términos y Condiciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Aceptación de los términos</h6>
                <p>Al registrarse y utilizar EduLink, acepta cumplir con estos términos y condiciones.</p>
                
                <h6>2. Uso del servicio</h6>
                <p>EduLink es una plataforma educativa diseñada para facilitar la interacción entre profesores y estudiantes. Los usuarios deben utilizarla únicamente para fines educativos.</p>
                
                <h6>3. Cuentas de usuario</h6>
                <p>Los usuarios son responsables de mantener la confidencialidad de sus credenciales de acceso.</p>
                
                <h6>4. Contenido</h6>
                <p>Los usuarios son responsables del contenido que comparten en la plataforma. No se permite contenido inapropiado, ofensivo o ilegal.</p>
                
                <h6>5. Privacidad</h6>
                <p>EduLink se compromete a proteger la privacidad de sus usuarios. Los datos personales serán tratados conforme a la política de privacidad.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
require_once 'views/partials/footer.php';
?>