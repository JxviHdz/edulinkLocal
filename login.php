<?php
/**
 * Página de inicio de sesión
 */

// Incluir configuración y funciones
require_once 'includes/config.php';
require_once 'includes/funciones.php';

// Si ya hay sesión iniciada, redirigir al inicio
if (estaAutenticado()) {
    redireccionar('index.php');
}

$error = '';

// Procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    } else {
        // Obtener datos del formulario
        $email = limpiarDatos($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validar datos
        if (empty($email) || empty($password)) {
            $error = 'Todos los campos son obligatorios';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido';
        } else {
            // Intentar autenticar al usuario
            $resultado = autenticarUsuario($email, $password);
            
            if ($resultado['exito']) {
                // Redirigir al inicio
                setMensaje('success', '¡Bienvenido de nuevo!');
                redireccionar('index.php');
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
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form action="login.php" method="post" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">¿No tienes una cuenta? <a href="<?php echo BASE_URL; ?>/registro.php">Regístrate</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
require_once 'views/partials/footer.php';
?>