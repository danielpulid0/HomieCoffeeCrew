<?php

require_once __DIR__ . '/database.php';

function obtener_conexion() {
    global $db;
    return $db;
}

function ejecutar_consulta($sql) {
    $db = obtener_conexion();
    if (!$db) return false;

    return mysqli_query($db, $sql);
}

function obtener_bebidas() {
    $sql = "SELECT id_bebida, imagen_url, nombre, categoria, precio, descripcion, estado_disponibilidad
            FROM bebidas
            ORDER BY categoria ASC, nombre ASC;";
    return ejecutar_consulta($sql);
}

function obtener_bebidas_disponibles() {
    $sql = "SELECT id_bebida, imagen_url, nombre, categoria, precio, descripcion
            FROM bebidas
            WHERE estado_disponibilidad = 1
            ORDER BY categoria ASC, nombre ASC;";
    return ejecutar_consulta($sql);
}

function obtener_promociones() {
    $sql = "SELECT id_promocion, titulo, descripcion, fecha_inicio, fecha_vigencia, imagen_url, estado_activo
            FROM promociones
            ORDER BY fecha_vigencia DESC;";
    return ejecutar_consulta($sql);
}

function obtener_promociones_vigentes() {
    $sql = "SELECT id_promocion, titulo, descripcion, fecha_inicio, fecha_vigencia, imagen_url
            FROM promociones
            WHERE estado_activo = 1
              AND CURDATE() BETWEEN fecha_inicio AND fecha_vigencia
            ORDER BY fecha_vigencia ASC;";
    return ejecutar_consulta($sql);
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
    $sql = "SELECT id_usuario, nombre, correo, sellos_actuales
            FROM usuarios
            WHERE rol = 'cliente'
            ORDER BY nombre ASC;";
    return ejecutar_consulta($sql);
}

function guardar_bebida($id, $nombre, $descripcion, $precio, $categoria, $estado) {
    $db = obtener_conexion();
    if (!$db) return false;

    if ($id > 0) {
        $consulta = mysqli_prepare($db, 'UPDATE bebidas SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, estado_disponibilidad = ? WHERE id_bebida = ?;');
        if (!$consulta) return false;
        mysqli_stmt_bind_param($consulta, 'ssdsii', $nombre, $descripcion, $precio, $categoria, $estado, $id);
    } else {
        $consulta = mysqli_prepare($db, 'INSERT INTO bebidas (nombre, descripcion, precio, categoria, estado_disponibilidad) VALUES (?, ?, ?, ?, ?);');
        if (!$consulta) return false;
        mysqli_stmt_bind_param($consulta, 'ssdsi', $nombre, $descripcion, $precio, $categoria, $estado);
    }

    return mysqli_stmt_execute($consulta);
}

function guardar_promocion($id, $titulo, $inicio, $vencimiento, $estado) {
    $db = obtener_conexion();
    if (!$db) return false;

    if ($id > 0) {
        $consulta = mysqli_prepare($db, 'UPDATE promociones SET titulo = ?, fecha_inicio = ?, fecha_vigencia = ?, estado_activo = ? WHERE id_promocion = ?;');
        if (!$consulta) return false;
        mysqli_stmt_bind_param($consulta, 'sssii', $titulo, $inicio, $vencimiento, $estado, $id);
    } else {
        $consulta = mysqli_prepare($db, 'INSERT INTO promociones (titulo, fecha_inicio, fecha_vigencia, estado_activo) VALUES (?, ?, ?, ?);');
        if (!$consulta) return false;
        mysqli_stmt_bind_param($consulta, 'sssi', $titulo, $inicio, $vencimiento, $estado);
    }

    return mysqli_stmt_execute($consulta);
}

function eliminar_registro($entidad, $id) {
    $db = obtener_conexion();
    $tablas = array(
        'bebida' => array('bebidas', 'id_bebida'),
        'promocion' => array('promociones', 'id_promocion')
    );
    if (!$db || !isset($tablas[$entidad]) || $id <= 0) return false;

    list($tabla, $columna_id) = $tablas[$entidad];
    $consulta = mysqli_prepare($db, "DELETE FROM $tabla WHERE $columna_id = ? LIMIT 1;");
    if (!$consulta) return false;

    mysqli_stmt_bind_param($consulta, 'i', $id);
    return mysqli_stmt_execute($consulta);
}

function sumar_sello($id_usuario) {
    $db = obtener_conexion();
    if (!$db || $id_usuario <= 0) return false;

    $consulta = mysqli_prepare($db, "UPDATE usuarios SET sellos_actuales = LEAST(sellos_actuales + 1, 10) WHERE id_usuario = ? AND rol = 'cliente';");
    if (!$consulta) return false;

    mysqli_stmt_bind_param($consulta, 'i', $id_usuario);
    return mysqli_stmt_execute($consulta);
}

function canjear_premio($id_usuario) {
    $db = obtener_conexion();
    if (!$db || $id_usuario <= 0) return false;

    $consulta = mysqli_prepare($db, "UPDATE usuarios SET sellos_actuales = 0 WHERE id_usuario = ? AND rol = 'cliente' AND sellos_actuales >= 10;");
    if (!$consulta) return false;

    mysqli_stmt_bind_param($consulta, 'i', $id_usuario);
    return mysqli_stmt_execute($consulta);
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

function obtener_usuario_sesion() {
    iniciar_sesion();
    return $_SESSION['usuario'] ?? null;
}

function requerir_empleado($ruta_login = '../login.php') {
    $usuario = obtener_usuario_sesion();

    if (!$usuario || !in_array($usuario['rol'], array('admin', 'barista'), true)) {
        header('Location: ' . $ruta_login);
        exit;
    }
}

function renderizar_navbar($prefijo = '') {
    $usuario = obtener_usuario_sesion();
    $ruta_prefijo = escapar($prefijo);
    ?>
    <header>
        <nav>
            <a href="<?= $ruta_prefijo ?>index.php" class="nombre-y-logo">
                <img src="<?= $ruta_prefijo ?>img/homie_logo.png" alt="Logo Homie Coffee Crew">
                <p class="nombre-marca">Homie Coffee Crew</p>
            </a>
            <div class="menu-links">
                <a href="<?= $ruta_prefijo ?>menu.php">Menú</a>
                <a href="<?= $ruta_prefijo ?>recompensas.php">Recompensas</a>
            </div>
            <div class="auth-buttons">
                <?php if ($usuario): ?>
                    <a href="<?= $ruta_prefijo ?>login.php?cerrar_sesion=1" class="btn-icono-salir" aria-label="Cerrar sesión" title="Cerrar sesión">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </a>
                <?php else: ?>
                    <a href="<?= $ruta_prefijo ?>login.php" class="btn btn-outline">Iniciar sesión</a>
                    <a href="<?= $ruta_prefijo ?>registro.php" class="btn btn-solid">Unirse al crew</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <?php
}

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function obtener_post_texto($clave, $recortar = true) {
    if (!isset($_POST[$clave]) || !is_string($_POST[$clave])) {
        return '';
    }

    return $recortar ? trim($_POST[$clave]) : $_POST[$clave];
}

function normalizar_sellos($sellos) {
    return max(0, min(10, (int) $sellos));
}

function formatear_codigo_miembro($id_usuario) {
    return 'HOMMIE-' . str_pad((string) $id_usuario, 3, '0', STR_PAD_LEFT);
}
