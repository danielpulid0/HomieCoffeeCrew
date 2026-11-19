<?php

require_once __DIR__ . '/database.php';

function obtener_conexion() {
    global $db;
    return $db;
}

function obtener_bebidas() {
    $db = obtener_conexion();
    if (!$db) return false;

    $sql = "SELECT id_bebida, imagen_url, nombre, categoria, precio, descripcion, estado_disponibilidad
            FROM bebidas
            ORDER BY categoria ASC, nombre ASC;";
    return mysqli_query($db, $sql);
}

function obtener_bebidas_disponibles() {
    $db = obtener_conexion();
    if (!$db) return false;

    $sql = "SELECT id_bebida, imagen_url, nombre, categoria, precio, descripcion
            FROM bebidas
            WHERE estado_disponibilidad = 1
            ORDER BY categoria ASC, nombre ASC;";
    return mysqli_query($db, $sql);
}

function obtener_promociones() {
    $db = obtener_conexion();
    if (!$db) return false;

    $sql = "SELECT id_promocion, titulo, descripcion, fecha_inicio, fecha_vigencia, imagen_url, estado_activo
            FROM promociones
            ORDER BY fecha_vigencia DESC;";
    return mysqli_query($db, $sql);
}

function obtener_promociones_vigentes() {
    $db = obtener_conexion();
    if (!$db) return false;

    $sql = "SELECT id_promocion, titulo, descripcion, fecha_inicio, fecha_vigencia, imagen_url
            FROM promociones
            WHERE estado_activo = 1
              AND CURDATE() BETWEEN fecha_inicio AND fecha_vigencia
            ORDER BY fecha_vigencia ASC;";
    return mysqli_query($db, $sql);
}

function obtener_datos_cliente($id_usuario) {
    $db = obtener_conexion();
    if (!$db) return false;

    $consulta = mysqli_prepare(
        $db,
        "SELECT id_usuario, nombre, correo, sellos_actuales, fecha_registro
         FROM usuarios
         WHERE id_usuario = ? AND rol = 'cliente'
         LIMIT 1;"
    );
    if (!$consulta) return false;

    $id = (int) $id_usuario;
    mysqli_stmt_bind_param($consulta, 'i', $id);
    if (!mysqli_stmt_execute($consulta)) return false;

    $resultado = mysqli_stmt_get_result($consulta);
    return $resultado ? mysqli_fetch_assoc($resultado) : false;
}

function obtener_clientes() {
    $db = obtener_conexion();
    if (!$db) return false;

    $sql = "SELECT id_usuario, nombre, correo, sellos_actuales
            FROM usuarios
            WHERE rol = 'cliente'
            ORDER BY nombre ASC;";
    return mysqli_query($db, $sql);
}

function obtener_kpis_reportes() {
    $db = obtener_conexion();
    if (!$db) return false;

    $sql = "SELECT
                (SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente') AS total_miembros,
                (SELECT COUNT(*) FROM promociones WHERE estado_activo = 1 AND CURDATE() BETWEEN fecha_inicio AND fecha_vigencia) AS promos_activas,
                (SELECT COUNT(*) FROM historial_fidelizacion WHERE tipo_movimiento = 'sello_otorgado') AS sellos_otorgados,
                (SELECT COUNT(*) FROM historial_fidelizacion WHERE tipo_movimiento = 'premio_canjeado') AS bebidas_canjeadas;";
    $consulta = mysqli_query($db, $sql);
    return $consulta ? mysqli_fetch_assoc($consulta) : false;
}

function registrar_cliente($nombre, $correo, $password) {
    $db = obtener_conexion();
    if (!$db) return false;

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $consulta = mysqli_prepare(
        $db,
        "INSERT INTO usuarios (nombre, correo, password, rol, sellos_actuales)
         VALUES (?, ?, ?, 'cliente', 0);"
    );
    if (!$consulta) return false;

    mysqli_stmt_bind_param($consulta, 'sss', $nombre, $correo, $hash);
    if (mysqli_stmt_execute($consulta)) return true;

    return mysqli_stmt_errno($consulta) === 1062 ? 'correo_existente' : false;
}

function autenticar_usuario($correo, $password) {
    $db = obtener_conexion();
    if (!$db) return false;

    $consulta = mysqli_prepare(
        $db,
        "SELECT id_usuario, nombre, correo, password, rol, sellos_actuales
         FROM usuarios
         WHERE correo = ?
         LIMIT 1;"
    );
    if (!$consulta) return false;

    mysqli_stmt_bind_param($consulta, 's', $correo);
    if (!mysqli_stmt_execute($consulta)) return false;

    $resultado = mysqli_stmt_get_result($consulta);
    if (!$resultado) return false;

    $usuario = mysqli_fetch_assoc($resultado);
    if (!$usuario || !password_verify($password, $usuario['password'])) return null;

    unset($usuario['password']);
    return $usuario;
}

function iniciar_sesion() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function requerir_empleado($ruta_login = '../login.php') {
    iniciar_sesion();
    $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

    if (!$usuario || !in_array($usuario['rol'], array('admin', 'barista'), true)) {
        header('Location: ' . $ruta_login);
        exit;
    }
}

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
