<?php
require_once __DIR__ . '/../includes/funciones.php';
requerir_empleado();
require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['csrf_catalogo']) || !is_string($_SESSION['csrf_catalogo'])) {
    $_SESSION['csrf_catalogo'] = bin2hex(random_bytes(32));
}
$csrf_catalogo = $_SESSION['csrf_catalogo'];
$error_catalogo = '';
$resultado_guardado = isset($_GET['guardado']) ? $_GET['guardado'] : '';

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = isset($_POST['tipo']) && is_string($_POST['tipo']) ? $_POST['tipo'] : '';

    if (
        !isset($_POST['csrf_catalogo']) ||
        !is_string($_POST['csrf_catalogo']) ||
        !hash_equals($csrf_catalogo, $_POST['csrf_catalogo'])
    ) {
        $error_catalogo = 'La solicitud expiró. Recarga la página e inténtalo de nuevo.';
    } elseif (!$db) {
        $error_catalogo = 'No fue posible conectar con la base de datos.';
    } elseif ($tipo === 'bebida') {
        $nombre = isset($_POST['nombre']) && is_string($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $categoria = isset($_POST['categoria']) && is_string($_POST['categoria']) ? $_POST['categoria'] : '';
        $descripcion = isset($_POST['descripcion']) && is_string($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $precio = isset($_POST['precio']) && is_string($_POST['precio'])
            ? filter_var($_POST['precio'], FILTER_VALIDATE_FLOAT)
            : false;
        $estado_texto = isset($_POST['estado']) && is_string($_POST['estado']) ? $_POST['estado'] : '';
        $estado = $estado_texto === 'Deshabilitado' ? 0 : 1;
        $categorias_validas = array('Tradicional', 'Alternativo', 'No Brew');

        if (
            $nombre === '' ||
            strlen($nombre) > 100 ||
            !in_array($categoria, $categorias_validas, true) ||
            $precio === false ||
            $precio < 0 ||
            !in_array($estado_texto, array('Habilitado', 'Deshabilitado'), true)
        ) {
            $error_catalogo = 'Revisa el nombre, la categoría y el precio de la bebida.';
        } else {
            $consulta = mysqli_prepare(
                $db,
                'INSERT INTO bebidas (nombre, descripcion, precio, categoria, estado_disponibilidad) VALUES (?, ?, ?, ?, ?);'
            );
            if ($consulta) {
                mysqli_stmt_bind_param($consulta, 'ssdsi', $nombre, $descripcion, $precio, $categoria, $estado);
            }
            if ($consulta && mysqli_stmt_execute($consulta)) {
                header('Location: catalogo.php?guardado=bebida');
                exit;
            }
            $error_catalogo = 'No se pudo guardar la bebida.';
        }
    } elseif ($tipo === 'promocion') {
        $titulo = isset($_POST['titulo']) && is_string($_POST['titulo']) ? trim($_POST['titulo']) : '';
        $inicio = isset($_POST['inicio']) && is_string($_POST['inicio']) ? $_POST['inicio'] : '';
        $vencimiento = isset($_POST['vencimiento']) && is_string($_POST['vencimiento']) ? $_POST['vencimiento'] : '';
        $estado_texto = isset($_POST['estado']) && is_string($_POST['estado']) ? $_POST['estado'] : '';
        $estado = $estado_texto === 'Deshabilitado' ? 0 : 1;
        $formato_fecha = 'Y-m-d';
        $fecha_inicio = DateTime::createFromFormat($formato_fecha, $inicio);
        $fecha_vencimiento = DateTime::createFromFormat($formato_fecha, $vencimiento);

        if (
            $titulo === '' ||
            !$fecha_inicio ||
            !$fecha_vencimiento ||
            strlen($titulo) > 120 ||
            $fecha_inicio->format($formato_fecha) !== $inicio ||
            $fecha_vencimiento->format($formato_fecha) !== $vencimiento ||
            $inicio > $vencimiento ||
            !in_array($estado_texto, array('Habilitado', 'Deshabilitado'), true)
        ) {
            $error_catalogo = 'Revisa el título y el periodo de vigencia de la promoción.';
        } else {
            $consulta = mysqli_prepare(
                $db,
                'INSERT INTO promociones (titulo, fecha_inicio, fecha_vigencia, estado_activo) VALUES (?, ?, ?, ?);'
            );
            if ($consulta) {
                mysqli_stmt_bind_param($consulta, 'sssi', $titulo, $inicio, $vencimiento, $estado);
            }
            if ($consulta && mysqli_stmt_execute($consulta)) {
                header('Location: catalogo.php?guardado=promocion');
                exit;
            }
            $error_catalogo = 'No se pudo guardar la promoción.';
        }
    } else {
        $error_catalogo = 'El tipo de elemento solicitado no es válido.';
    }
}

$resultado_bebidas = obtener_bebidas();
$resultado_promociones = obtener_promociones();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Catálogo | Homie Coffee Crew</title>
    <link rel="stylesheet" href="../css/styles2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <a href="fidelizacion.php" class="nombre-y-logo">
                <img src="../img/homie_logo.png" alt="Logo Homie Coffee Crew">
                <span class="nombre-marca">Homie Coffee Crew</span>
            </a>
            <div class="menu-links">
                <a href="fidelizacion.php">Fidelización</a>
                <a href="catalogo.php" class="activo">Catálogo</a>
                <a href="reportes.php">Reportes y Métricas</a>
            </div>
            <div class="auth-buttons">
                <a href="../login.php?cerrar_sesion=1" class="btn-icono-salir" aria-label="Cerrar sesión" title="Cerrar sesión">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            </div>
        </nav>
    </header>

    <main id="catalogo">
        <div class="fila-flexible">
            <h1 class="admin-h1">Gestión de catálogo</h1>
            <button type="button" class="btn btn-solid" id="abrir-modal-item">Agregar item</button>
        </div>
        <?php if ($error_catalogo !== ''): ?>
            <p class="mensaje-error" role="alert"><?= escapar($error_catalogo) ?></p>
        <?php elseif ($resultado_guardado === 'bebida' || $resultado_guardado === 'promocion'): ?>
            <p class="mensaje-exito" role="status">El registro se guardó correctamente.</p>
        <?php endif; ?>

        <section class="panel-tabs">
            <div class="tabs-header" role="tablist">
                <button type="button" class="tab-btn activo" data-tab="tab-bebidas">Bebidas</button>
                <button type="button" class="tab-btn" data-tab="tab-promociones">Promociones</button>
            </div>

            <div id="tab-bebidas" class="columna-de-elementos">
                <div class="fila-flexible">
                    <div class="buscador-input">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="search" placeholder="Buscar" aria-label="Buscar bebida">
                    </div>
                </div>
                <div class="tabla-contenedor">
                    <table class="tabla-admin">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$resultado_bebidas): ?>
                                <tr><td colspan="5">No fue posible cargar las bebidas.</td></tr>
                            <?php elseif (mysqli_num_rows($resultado_bebidas) === 0): ?>
                                <tr><td colspan="5">No hay bebidas registradas.</td></tr>
                            <?php else: ?>
                                <?php while ($bebida = mysqli_fetch_assoc($resultado_bebidas)): ?>
                                    <tr>
                                        <td><?= escapar($bebida['nombre']) ?></td>
                                        <td><?= escapar($bebida['categoria']) ?></td>
                                        <td><?= escapar($bebida['descripcion']) ?></td>
                                        <td>$<?= number_format((float) $bebida['precio'], 2) ?></td>
                                        <td><?= $bebida['estado_disponibilidad'] ? 'Habilitado' : 'Deshabilitado' ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-promociones" class="columna-de-elementos oculto">
                <div class="fila-flexible">
                    <div class="buscador-input">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="search" placeholder="Buscar" aria-label="Buscar promoción">
                    </div>
                </div>
                <div class="tabla-contenedor">
                    <table class="tabla-admin">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Inicio</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$resultado_promociones): ?>
                                <tr><td colspan="4">No fue posible cargar las promociones.</td></tr>
                            <?php elseif (mysqli_num_rows($resultado_promociones) === 0): ?>
                                <tr><td colspan="4">No hay promociones registradas.</td></tr>
                            <?php else: ?>
                                <?php while ($promocion = mysqli_fetch_assoc($resultado_promociones)): ?>
                                    <tr>
                                        <td><?= escapar($promocion['titulo']) ?></td>
                                        <td><?= escapar(date('d/m/Y', strtotime($promocion['fecha_inicio']))) ?></td>
                                        <td><?= escapar(date('d/m/Y', strtotime($promocion['fecha_vigencia']))) ?></td>
                                        <td><?= $promocion['estado_activo'] ? 'Habilitado' : 'Deshabilitado' ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <dialog class="modal-catalogo" id="modal-item" aria-labelledby="modal-titulo">
        <div class="fila-flexible modal-catalogo-header">
            <h2 id="modal-titulo">Agregar item</h2>
            <button type="button" class="btn-cerrar-modal" aria-label="Cerrar">×</button>
        </div>
        <div id="selector-tipo-item" class="formulario">
            <p>¿Qué deseas agregar?</p>
            <div class="columna-de-elementos">
                <button type="button" class="btn btn-solid btn-add" data-tipo-item="bebida">Bebida</button>
                <button type="button" class="btn btn-outline btn-add" data-tipo-item="promocion">Promoción</button>
            </div>
        </div>

        <form id="form-bebida" class="formulario" action="catalogo.php" method="post" hidden>
            <input type="hidden" name="tipo" value="bebida">
            <input type="hidden" name="csrf_catalogo" value="<?= escapar($csrf_catalogo) ?>">
            <div class="grupo-input">
                <label for="bebida-nombre">Nombre</label>
                <input id="bebida-nombre" name="nombre" type="text" maxlength="100" required>
            </div>
            <div class="grupo-input">
                <label for="bebida-categoria">Categoría</label>
                <select id="bebida-categoria" name="categoria" required>
                    <option value="Tradicional">Tradicional</option>
                    <option value="Alternativo">Alternativo</option>
                    <option value="No Brew">No Brew</option>
                </select>
            </div>
            <div class="grupo-input">
                <label for="bebida-descripcion">Descripción</label>
                <input id="bebida-descripcion" name="descripcion" type="text" required>
            </div>
            <div class="grupo-input">
                <label for="bebida-precio">Precio</label>
                <input id="bebida-precio" name="precio" type="number" min="0" step="0.01" required>
            </div>
            <div class="grupo-input">
                <label for="bebida-estado">Estado</label>
                <select id="bebida-estado" name="estado">
                    <option>Habilitado</option>
                    <option>Deshabilitado</option>
                </select>
            </div>
            <div class="fila-flexible modal-acciones">
                <button type="button" class="btn btn-outline volver-selector">Volver</button>
                <button type="submit" class="btn btn-solid">Agregar bebida</button>
            </div>
        </form>

        <form id="form-promocion" class="formulario" action="catalogo.php" method="post" hidden>
            <input type="hidden" name="tipo" value="promocion">
            <input type="hidden" name="csrf_catalogo" value="<?= escapar($csrf_catalogo) ?>">
            <div class="grupo-input">
                <label for="promocion-titulo">Título</label>
                <input id="promocion-titulo" name="titulo" type="text" maxlength="120" required>
            </div>
            <div class="grupo-input">
                <label for="promocion-inicio">Fecha de inicio</label>
                <input id="promocion-inicio" name="inicio" type="date" required>
            </div>
            <div class="grupo-input">
                <label for="promocion-vencimiento">Fecha de vencimiento</label>
                <input id="promocion-vencimiento" name="vencimiento" type="date" required>
            </div>
            <div class="grupo-input">
                <label for="promocion-estado">Estado</label>
                <select id="promocion-estado" name="estado">
                    <option>Habilitado</option>
                    <option>Deshabilitado</option>
                </select>
            </div>
            <div class="fila-flexible modal-acciones">
                <button type="button" class="btn btn-outline volver-selector">Volver</button>
                <button type="submit" class="btn btn-solid">Agregar promoción</button>
            </div>
        </form>
    </dialog>
    <script src="../js/catalogo.js"></script>
</body>
</html>
