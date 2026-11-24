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

function obtener_kpis_reportes() {
    $sql = "SELECT
                (SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente') AS total_miembros,
                (SELECT COUNT(*) FROM promociones WHERE estado_activo = 1 AND CURDATE() BETWEEN fecha_inicio AND fecha_vigencia) AS promos_activas,
                (SELECT COUNT(*) FROM historial_fidelizacion WHERE tipo_movimiento = 'sello_otorgado') AS sellos_otorgados,
                (SELECT COUNT(*) FROM historial_fidelizacion WHERE tipo_movimiento = 'premio_canjeado') AS bebidas_canjeadas;";
    $resultado = ejecutar_consulta($sql);
    return $resultado ? mysqli_fetch_assoc($resultado) : false;
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
                    <a href="<?= $ruta_prefijo ?>cliente/registro.php" class="btn btn-solid">Unirse al crew</a>
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
