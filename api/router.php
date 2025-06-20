<?php
// Router genérico para ejecutar cualquier script PHP existente en la raíz del proyecto.
// Vercel solo ejecuta funciones dentro de la carpeta "api"; este archivo sirve como puente.

// Determinar el archivo solicitado.
$script = $_GET['file'] ?? 'index.php';

// Seguridad básica: impedir rutas que salgan del directorio
$script = basename($script);

// Ruta absoluta al archivo en la raíz del proyecto
$path = __DIR__ . '/../' . $script;

if (!file_exists($path)) {
    http_response_code(404);
    echo "Archivo no encontrado";
    exit;
}

// Ejecutar el script solicitado
require $path;
?> 