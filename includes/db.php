<?php
/**
 * Archivo de configuración para la conexión a la base de datos
 * Este archivo contiene los parámetros de conexión a MySQL
 */

// Parámetros de conexión a la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Usuario por defecto de XAMPP
define('DB_PASS', '');     // Contraseña por defecto de XAMPP (vacía)
define('DB_NAME', 'edulink');

// Función para establecer la conexión a la base de datos
function conectarDB() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión a la base de datos: " . $conexion->connect_error);
    }
    
    // Establecer codificación UTF-8
    $conexion->set_charset("utf8mb4");
    
    return $conexion;
}

// Función para ejecutar consultas seguras con prepared statements
function consultaSQL($sql, $params = [], $tipos = "") {
    $conexion = conectarDB();
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        die("Error en la consulta: " . $conexion->error);
    }
    
    // Si hay parámetros, enlazarlos
    if (!empty($params)) {
        if (empty($tipos)) {
            // Determinar automáticamente los tipos
            $tipos = "";
            foreach ($params as $param) {
                if (is_int($param)) {
                    $tipos .= "i"; // integer
                } elseif (is_double($param) || is_float($param)) {
                    $tipos .= "d"; // double
                } elseif (is_string($param)) {
                    $tipos .= "s"; // string
                } else {
                    $tipos .= "b"; // blob
                }
            }
        }
        
        $stmt->bind_param($tipos, ...$params);
    }
    
    $stmt->execute();
    
    $resultado = $stmt->get_result();
    $stmt->close();
    $conexion->close();
    
    return $resultado;
}

// Función para obtener un solo registro
function obtenerRegistro($sql, $params = [], $tipos = "") {
    $resultado = consultaSQL($sql, $params, $tipos);
    
    if ($resultado && $resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    }
    
    return null;
}

// Función para obtener múltiples registros
function obtenerRegistros($sql, $params = [], $tipos = "") {
    $resultado = consultaSQL($sql, $params, $tipos);
    $registros = [];
    
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $registros[] = $fila;
        }
    }
    
    return $registros;
}

// Función para insertar registros y devolver el ID
function insertarRegistro($sql, $params = [], $tipos = "") {
    $conexion = conectarDB();
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        die("Error en la consulta: " . $conexion->error);
    }
    
    // Si hay parámetros, enlazarlos
    if (!empty($params)) {
        if (empty($tipos)) {
            // Determinar automáticamente los tipos
            $tipos = "";
            foreach ($params as $param) {
                if (is_int($param)) {
                    $tipos .= "i"; // integer
                } elseif (is_double($param) || is_float($param)) {
                    $tipos .= "d"; // double
                } elseif (is_string($param)) {
                    $tipos .= "s"; // string
                } else {
                    $tipos .= "b"; // blob
                }
            }
        }
        
        $stmt->bind_param($tipos, ...$params);
    }
    
    $stmt->execute();
    $id_insertado = $conexion->insert_id;
    
    $stmt->close();
    $conexion->close();
    
    return $id_insertado;
}