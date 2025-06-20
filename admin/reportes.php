<?php
/**
 * Página de gestión de reportes (solo para administradores)
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

// Aprobar o eliminar comentario reportado
if ($accion === 'aprobar' && $id) {
    if (!verificarTokenCSRF($_GET['csrf_token'] ?? '')) {
        setMensaje('danger', 'Error de seguridad. Por favor, intente nuevamente.');
        redireccionar('admin/reportes.php');
    } else {
        $conexion = conectarDB();
        $stmt = $conexion->prepare("UPDATE foros_comentarios SET reportado = 0 WHERE id = ?");
        if (!$stmt) {
            setMensaje('danger', 'Error en la consulta: ' . $conexion->error);
            redireccionar('admin/reportes.php');
        }
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conexion->close();

        setMensaje($resultado ? 'success' : 'danger', $resultado ? 'Comentario aprobado correctamente' : 'Error al aprobar el comentario');
        redireccionar('admin/reportes.php');
    }
} elseif ($accion === 'eliminar' && $id) {
    if (!verificarTokenCSRF($_GET['csrf_token'] ?? '')) {
        setMensaje('danger', 'Error de seguridad. Por favor, intente nuevamente.');
        redireccionar('admin/reportes.php');
    } else {
        $conexion = conectarDB();
        $stmt = $conexion->prepare("DELETE FROM foros_comentarios WHERE id = ?");
        if (!$stmt) {
            setMensaje('danger', 'Error en la consulta: ' . $conexion->error);
            redireccionar('admin/reportes.php');
        }
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $conexion->close();

        setMensaje($resultado ? 'success' : 'danger', $resultado ? 'Comentario eliminado correctamente' : 'Error al eliminar el comentario');
        redireccionar('admin/reportes.php');
    }
}

// Obtener comentarios reportados
$comentarios_reportados = obtenerComentariosReportados();

// Incluir el header (verificamos si ya está definida BASE_URL)
if (!defined('BASE_URL')) {
    define('BASE_URL', 'edulink/');
}
require_once '../views/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-flag me-2"></i>Gestión de Reportes</h2>
</div>

<?php if (empty($comentarios_reportados)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>No hay comentarios reportados.
</div>
<?php else: ?>
<div class="card shadow mb-4">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Comentarios Reportados (<?php echo count($comentarios_reportados); ?>)</h4>
    </div>
    <div class="card-body">
        <?php foreach ($comentarios_reportados as $comentario): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <strong><?php echo $comentario['nombre'] . ' ' . $comentario['apellido']; ?></strong>
                        <?php if (!empty($comentario['rol'])): ?>
    <span class="badge bg-<?php echo $comentario['rol'] === 'administrador' ? 'danger' : ($comentario['rol'] === 'profesor' ? 'primary' : 'success'); ?> ms-1">
        <?php echo ucfirst($comentario['rol']); ?>
    </span>
<?php endif; ?>

                    </span>
                    <small class="text-muted">
                        <i class="far fa-calendar-alt me-1"></i><?php echo date('d/m/Y H:i', strtotime($comentario['fecha_creacion'])); ?>
                    </small>
                </div>
            </div>
            <div class="card-body">
                <h5>Tema: <?php echo $comentario['tema_titulo']; ?></h5>
                <div class="p-3 bg-light rounded mb-3">
                    <?php echo nl2br($comentario['contenido']); ?>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>Este comentario ha sido reportado por un usuario.
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?php echo BASE_URL; ?>admin/reportes.php?accion=aprobar&id=<?php echo $comentario['id']; ?>&csrf_token=<?php echo generarTokenCSRF(); ?>" class="btn btn-success me-2">
                        <i class="fas fa-check me-1"></i>Aprobar
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/reportes.php?accion=eliminar&id=<?php echo $comentario['id']; ?>&csrf_token=<?php echo generarTokenCSRF(); ?>" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i>Eliminar
                    </a>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?php echo BASE_URL; ?>foros.php?id=<?php echo $comentario['id_tema']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>Ver en Contexto
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="mt-4">
    <a href="/edulink/index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver al Inicio
    </a>
</div>

<?php
// Incluir el footer
require_once '../views/partials/footer.php';
?>
