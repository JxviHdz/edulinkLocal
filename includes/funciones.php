<?php
/**
 * Funciones auxiliares para la aplicación
 */

// Incluir configuraciones
require_once 'config.php';
require_once 'db.php';

// Función para registrar un nuevo usuario
function registrarUsuario($nombre, $apellido, $email, $password, $rol) {
    // Verificar si el correo ya existe
    $usuario_existe = obtenerRegistro(
        "SELECT id FROM usuarios WHERE email = ?",
        [$email]
    );
    
    if ($usuario_existe) {
        return [
            'exito' => false,
            'mensaje' => 'El correo electrónico ya está registrado'
        ];
    }
    
    // Hashear la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar el nuevo usuario
    $id = insertarRegistro(
        "INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES (?, ?, ?, ?, ?)",
        [$nombre, $apellido, $email, $password_hash, $rol]
    );
    
    if ($id) {
        return [
            'exito' => true,
            'mensaje' => 'Usuario registrado correctamente',
            'id' => $id
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al registrar el usuario'
    ];
}

// Función para autenticar un usuario
function autenticarUsuario($email, $password) {
    $usuario = obtenerRegistro(
        "SELECT id, nombre, apellido, email, password, rol FROM usuarios WHERE email = ?",
        [$email]
    );
    
    if (!$usuario) {
        return [
            'exito' => false,
            'mensaje' => 'Credenciales incorrectas'
        ];
    }
    
    // Verificar la contraseña
    if (password_verify($password, $usuario['password'])) {
        // Establecer la sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        
        return [
            'exito' => true,
            'mensaje' => 'Inicio de sesión exitoso'
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Credenciales incorrectas'
    ];
}

// Función para cerrar sesión
function cerrarSesion() {
    session_start();
    session_unset();
    session_destroy();
    
    // Redireccionar al inicio
    redireccionar('index.php');
}

// Función para obtener información del usuario actual
function obtenerUsuarioActual() {
    if (!estaAutenticado()) {
        return null;
    }
    
    return obtenerRegistro(
        "SELECT id, nombre, apellido, email, rol FROM usuarios WHERE id = ?",
        [$_SESSION['usuario_id']]
    );
}

// Función para subir archivos
function subirArchivo($archivo, $tipos_permitidos = ['image/jpeg', 'image/png', 'application/pdf']) {
    // Verificar si hay errores
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return [
            'exito' => false,
            'mensaje' => 'Error al subir el archivo: ' . $archivo['error']
        ];
    }
    
    // Verificar el tipo de archivo
    if (!in_array($archivo['type'], $tipos_permitidos)) {
        return [
            'exito' => false,
            'mensaje' => 'Tipo de archivo no permitido. Solo se permiten imágenes y PDF'
        ];
    }
    
    // Verificar tamaño (máximo 5MB)
    if ($archivo['size'] > 5 * 1024 * 1024) {
        return [
            'exito' => false,
            'mensaje' => 'El archivo es demasiado grande. Máximo 5MB'
        ];
    }
    
    // Crear directorio si no existe
    if (!file_exists(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0777, true);
    }
    
    // Generar nombre único
    $nombre_archivo = uniqid() . '_' . $archivo['name'];
    $ruta_destino = UPLOADS_DIR . $nombre_archivo;
    
    // Mover el archivo
    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        return [
            'exito' => true,
            'mensaje' => 'Archivo subido correctamente',
            'nombre_archivo' => $nombre_archivo,
            'tipo_archivo' => $archivo['type']
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al mover el archivo'
    ];
}

// Función para crear un nuevo material educativo
function crearMaterial($titulo, $descripcion, $archivo, $id_usuario) {
    // Subir el archivo
    $resultado_subida = subirArchivo($archivo);
    
    if (!$resultado_subida['exito']) {
        return $resultado_subida;
    }
    
    // Insertar en la base de datos
    $id = insertarRegistro(
        "INSERT INTO materiales (titulo, descripcion, archivo, tipo_archivo, id_usuario) VALUES (?, ?, ?, ?, ?)",
        [$titulo, $descripcion, $resultado_subida['nombre_archivo'], $resultado_subida['tipo_archivo'], $id_usuario]
    );
    
    if ($id) {
        return [
            'exito' => true,
            'mensaje' => 'Material creado correctamente',
            'id' => $id
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al crear el material'
    ];
}

// Función para obtener materiales educativos
function obtenerMateriales() {
    return obtenerRegistros(
        "SELECT m.*, u.nombre, u.apellido FROM materiales m 
         INNER JOIN usuarios u ON m.id_usuario = u.id 
         ORDER BY m.fecha_creacion DESC"
    );
}

// Función para obtener un material específico
function obtenerMaterial($id) {
    return obtenerRegistro(
        "SELECT m.*, u.nombre, u.apellido FROM materiales m 
         INNER JOIN usuarios u ON m.id_usuario = u.id 
         WHERE m.id = ?",
        [$id]
    );
}

// Función para crear un nuevo tema en el foro
function crearTemaForo($titulo, $contenido, $id_usuario) {
    $id = insertarRegistro(
        "INSERT INTO foros_temas (titulo, contenido, id_usuario) VALUES (?, ?, ?)",
        [$titulo, $contenido, $id_usuario]
    );
    
    if ($id) {
        return [
            'exito' => true,
            'mensaje' => 'Tema creado correctamente',
            'id' => $id
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al crear el tema'
    ];
}

// Función para obtener temas del foro
function obtenerTemasForo() {
    return obtenerRegistros(
        "SELECT t.*, u.nombre, u.apellido, 
         (SELECT COUNT(*) FROM foros_comentarios WHERE id_tema = t.id) AS num_comentarios
         FROM foros_temas t 
         INNER JOIN usuarios u ON t.id_usuario = u.id 
         ORDER BY t.fecha_creacion DESC"
    );
}

// Función para obtener un tema específico con sus comentarios
function obtenerTemaForoConComentarios($id_tema) {
    $tema = obtenerRegistro(
        "SELECT t.*, u.nombre, u.apellido FROM foros_temas t 
         INNER JOIN usuarios u ON t.id_usuario = u.id 
         WHERE t.id = ?",
        [$id_tema]
    );
    
    if (!$tema) {
        return null;
    }
    
    $comentarios = obtenerRegistros(
        "SELECT c.*, u.nombre, u.apellido, u.rol FROM foros_comentarios c 
         INNER JOIN usuarios u ON c.id_usuario = u.id 
         WHERE c.id_tema = ? 
         ORDER BY c.fecha_creacion ASC",
        [$id_tema]
    );
    
    $tema['comentarios'] = $comentarios;
    
    return $tema;
}

// Función para crear un comentario en un tema
function crearComentarioForo($id_tema, $contenido, $id_usuario) {
    $id = insertarRegistro(
        "INSERT INTO foros_comentarios (id_tema, id_usuario, contenido) VALUES (?, ?, ?)",
        [$id_tema, $id_usuario, $contenido]
    );
    
    if ($id) {
        return [
            'exito' => true,
            'mensaje' => 'Comentario publicado correctamente',
            'id' => $id
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al publicar el comentario'
    ];
}

// Función para reportar un comentario
function reportarComentario($id_comentario) {
    $conexion = conectarDB();
    $stmt = $conexion->prepare("UPDATE foros_comentarios SET reportado = 1 WHERE id = ?");
    
    if (!$stmt) {
        return [
            'exito' => false,
            'mensaje' => 'Error en la consulta: ' . $conexion->error
        ];
    }
    
    $stmt->bind_param("i", $id_comentario);
    $resultado = $stmt->execute();
    
    $stmt->close();
    $conexion->close();
    
    if ($resultado) {
        return [
            'exito' => true,
            'mensaje' => 'Comentario reportado correctamente'
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al reportar el comentario'
    ];
}

// Función para obtener comentarios reportados (para administradores)
function obtenerComentariosReportados() {
    return obtenerRegistros(
        "SELECT c.*, u.nombre, u.apellido, t.titulo as tema_titulo 
         FROM foros_comentarios c 
         INNER JOIN usuarios u ON c.id_usuario = u.id 
         INNER JOIN foros_temas t ON c.id_tema = t.id 
         WHERE c.reportado = 1 
         ORDER BY c.fecha_creacion DESC"
    );
}

// Función para crear una evaluación
function crearEvaluacion($titulo, $descripcion, $id_profesor) {
    $id = insertarRegistro(
        "INSERT INTO evaluaciones (titulo, descripcion, id_profesor) VALUES (?, ?, ?)",
        [$titulo, $descripcion, $id_profesor]
    );
    
    if ($id) {
        return [
            'exito' => true,
            'mensaje' => 'Evaluación creada correctamente',
            'id' => $id
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al crear la evaluación'
    ];
}

// Función para añadir una pregunta a una evaluación
function agregarPregunta($id_evaluacion, $pregunta, $opcion_a, $opcion_b, $opcion_c, $opcion_d, $respuesta_correcta) {
    $id = insertarRegistro(
        "INSERT INTO preguntas (id_evaluacion, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$id_evaluacion, $pregunta, $opcion_a, $opcion_b, $opcion_c, $opcion_d, $respuesta_correcta]
    );
    
    if ($id) {
        return [
            'exito' => true,
            'mensaje' => 'Pregunta agregada correctamente',
            'id' => $id
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al agregar la pregunta'
    ];
}

// Función para obtener evaluaciones
function obtenerEvaluaciones() {
    return obtenerRegistros(
        "SELECT e.*, u.nombre, u.apellido, 
         (SELECT COUNT(*) FROM preguntas WHERE id_evaluacion = e.id) AS num_preguntas
         FROM evaluaciones e 
         INNER JOIN usuarios u ON e.id_profesor = u.id 
         ORDER BY e.fecha_creacion DESC"
    );
}

// Función para obtener una evaluación con sus preguntas
function obtenerEvaluacionConPreguntas($id_evaluacion) {
    $evaluacion = obtenerRegistro(
        "SELECT e.*, u.nombre, u.apellido FROM evaluaciones e 
         INNER JOIN usuarios u ON e.id_profesor = u.id 
         WHERE e.id = ?",
        [$id_evaluacion]
    );
    
    if (!$evaluacion) {
        return null;
    }
    
    $preguntas = obtenerRegistros(
        "SELECT * FROM preguntas WHERE id_evaluacion = ? ORDER BY id",
        [$id_evaluacion]
    );
    
    $evaluacion['preguntas'] = $preguntas;
    
    return $evaluacion;
}

// Función para guardar resultado de evaluación
function guardarResultadoEvaluacion($id_evaluacion, $id_estudiante, $puntaje) {
    $id = insertarRegistro(
        "INSERT INTO resultados (id_evaluacion, id_estudiante, puntaje) VALUES (?, ?, ?)",
        [$id_evaluacion, $id_estudiante, $puntaje]
    );
    
    if ($id) {
        return [
            'exito' => true,
            'mensaje' => 'Resultado guardado correctamente',
            'id' => $id
        ];
    }
    
    return [
        'exito' => false,
        'mensaje' => 'Error al guardar el resultado'
    ];
}

// Función para obtener los resultados de un estudiante
function obtenerResultadosEstudiante($id_estudiante) {
    return obtenerRegistros(
        "SELECT r.*, e.titulo, e.descripcion, 
         (SELECT COUNT(*) FROM preguntas WHERE id_evaluacion = r.id_evaluacion) AS total_preguntas 
         FROM resultados r 
         INNER JOIN evaluaciones e ON r.id_evaluacion = e.id 
         WHERE r.id_estudiante = ? 
         ORDER BY r.fecha_realizacion DESC",
        [$id_estudiante]
    );
}