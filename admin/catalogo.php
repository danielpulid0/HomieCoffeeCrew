<?php
require_once __DIR__ . '/../includes/funciones.php';
requerir_empleado();

$error_catalogo = '';
$resultado_guardado = $_GET['guardado'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = obtener_post_texto('tipo', false);

    if (!$db) {
        $error_catalogo = 'No fue posible conectar con la base de datos.';
    } elseif ($tipo === 'bebida') {
        $id = (int) ($_POST['id'] ?? 0);
        $nombre = obtener_post_texto('nombre');
        $categoria = obtener_post_texto('categoria', false);
        $descripcion = obtener_post_texto('descripcion');
        $precio_texto = $_POST['precio'] ?? '';
        $precio = (float) $precio_texto;
        $estado = obtener_post_texto('estado', false) === 'Deshabilitado' ? 0 : 1;

        if ($nombre === '' || $categoria === '' || !is_numeric($precio_texto) || $precio < 0) {
            $error_catalogo = 'Revisa el nombre, la categoría y el precio de la bebida.';
        } elseif (guardar_bebida($id, $nombre, $descripcion, $precio, $categoria, $estado)) {
            header('Location: catalogo.php?guardado=bebida');
            exit;
        } else {
            $error_catalogo = 'No se pudo guardar la bebida.';
        }
    } elseif ($tipo === 'promocion') {
        $id = (int) ($_POST['id'] ?? 0);
        $titulo = obtener_post_texto('titulo');
        $inicio = obtener_post_texto('inicio', false);
        $vencimiento = obtener_post_texto('vencimiento', false);
        $estado = obtener_post_texto('estado', false) === 'Deshabilitado' ? 0 : 1;

        if ($titulo === '' || $inicio === '' || $vencimiento === '' || $inicio > $vencimiento) {
            $error_catalogo = 'Revisa el título y el periodo de vigencia de la promoción.';
        } elseif (guardar_promocion($id, $titulo, $inicio, $vencimiento, $estado)) {
            header('Location: catalogo.php?guardado=promocion');
            exit;
        } else {
            $error_catalogo = 'No se pudo guardar la promoción.';
        }
    } elseif ($tipo === 'eliminar') {
        $entidad = obtener_post_texto('entidad', false);
        $id = (int) ($_POST['id'] ?? 0);

        if (!in_array($entidad, array('bebida', 'promocion'), true) || $id <= 0) {
            $error_catalogo = 'El elemento que intentas eliminar no es válido.';
        } elseif (eliminar_registro($entidad, $id)) {
            header('Location: catalogo.php?guardado=eliminado');
            exit;
        } else {
            $error_catalogo = 'No se pudo eliminar el registro.';
        }
    } else {
        $error_catalogo = 'El tipo de elemento solicitado no es válido.';
    }
}

function renderizar_acciones_fila($entidad, $item) {
    $es_bebida = $entidad === 'bebida';
    $id = (int) ($es_bebida ? $item['id_bebida'] : $item['id_promocion']);
    $nombre = $es_bebida ? $item['nombre'] : $item['titulo'];
    $estado = ($es_bebida ? $item['estado_disponibilidad'] : $item['estado_activo']) ? 'Habilitado' : 'Deshabilitado';
    ?>
    <td>
        <div class="acciones-tabla">
            <button
                type="button"
                class="btn-edit"
                data-editar-tipo="<?= escapar($entidad) ?>"
                data-id="<?= $id ?>"
                data-estado="<?= $estado ?>"
                <?php if ($es_bebida): ?>
                    data-nombre="<?= escapar($item['nombre']) ?>"
                    data-categoria="<?= escapar($item['categoria']) ?>"
                    data-descripcion="<?= escapar($item['descripcion']) ?>"
                    data-precio="<?= escapar($item['precio']) ?>"
                <?php else: ?>
                    data-titulo="<?= escapar($item['titulo']) ?>"
                    data-inicio="<?= escapar($item['fecha_inicio']) ?>"
                    data-vencimiento="<?= escapar($item['fecha_vigencia']) ?>"
                <?php endif; ?>
                aria-label="Editar"
                title="Editar"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
            </button>
            <button
                type="button"
                class="btn-trash"
                data-eliminar-entidad="<?= escapar($entidad) ?>"
                data-eliminar-id="<?= $id ?>"
                data-eliminar-nombre="<?= escapar($nombre) ?>"
                aria-label="Eliminar"
                title="Eliminar"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        </div>
    </td>
    <?php
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
    <link rel="stylesheet" href="../css/styles.css">
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

    <main id="catalogo" class="contenedor">
        <div class="fila-flexible">
            <h1>Gestión de catálogo</h1>
            <button type="button" class="btn btn-solid" id="abrir-modal-item">Agregar item</button>
        </div>
        <?php if ($error_catalogo !== ''): ?>
            <p class="mensaje-error" role="alert"><?= escapar($error_catalogo) ?></p>
        <?php elseif ($resultado_guardado === 'eliminado'): ?>
            <p class="mensaje-exito" role="status">El registro se eliminó correctamente.</p>
        <?php elseif ($resultado_guardado === 'bebida' || $resultado_guardado === 'promocion'): ?>
            <p class="mensaje-exito" role="status">El registro se guardó correctamente.</p>
        <?php endif; ?>

        <section class="panel-tabs">
            <div class="tabs-header" role="tablist">
                <button type="button" class="tab-btn activo" data-tab="tab-bebidas">Bebidas</button>
                <button type="button" class="tab-btn" data-tab="tab-promociones">Promociones</button>
            </div>

            <div id="tab-bebidas" class="columna-de-elementos tab">
                <div class="tabla-contenedor">
                    <table class="tabla-admin">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$resultado_bebidas): ?>
                                <tr><td colspan="6">No fue posible cargar las bebidas.</td></tr>
                            <?php elseif (mysqli_num_rows($resultado_bebidas) === 0): ?>
                                <tr><td colspan="6">No hay bebidas registradas.</td></tr>
                            <?php else: ?>
                                <?php while ($bebida = mysqli_fetch_assoc($resultado_bebidas)): ?>
                                    <tr>
                                        <td><?= escapar($bebida['nombre']) ?></td>
                                        <td><?= escapar($bebida['categoria']) ?></td>
                                        <td><?= escapar($bebida['descripcion']) ?></td>
                                        <td>$<?= number_format((float) $bebida['precio'], 2) ?></td>
                                        <td><?= $bebida['estado_disponibilidad'] ? 'Habilitado' : 'Deshabilitado' ?></td>
                                        <?php renderizar_acciones_fila('bebida', $bebida); ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-promociones" class="columna-de-elementos oculto tab">
                <div class="tabla-contenedor">
                    <table class="tabla-admin">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Inicio</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$resultado_promociones): ?>
                                <tr><td colspan="5">No fue posible cargar las promociones.</td></tr>
                            <?php elseif (mysqli_num_rows($resultado_promociones) === 0): ?>
                                <tr><td colspan="5">No hay promociones registradas.</td></tr>
                            <?php else: ?>
                                <?php while ($promocion = mysqli_fetch_assoc($resultado_promociones)): ?>
                                    <tr>
                                        <td><?= escapar($promocion['titulo']) ?></td>
                                        <td><?= escapar(date('d/m/Y', strtotime($promocion['fecha_inicio']))) ?></td>
                                        <td><?= escapar(date('d/m/Y', strtotime($promocion['fecha_vigencia']))) ?></td>
                                        <td><?= $promocion['estado_activo'] ? 'Habilitado' : 'Deshabilitado' ?></td>
                                        <?php renderizar_acciones_fila('promocion', $promocion); ?>
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

        <form id="form-bebida" class="formulario" action="catalogo.php" method="post" hidden novalidate>
            <p id="error-bebida" class="mensaje-error" role="alert" hidden></p>
            <input type="hidden" name="tipo" value="bebida">
            <input type="hidden" name="id" id="bebida-id">
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
                <button type="submit" class="btn btn-solid" id="btn-guardar-bebida">Agregar bebida</button>
            </div>
        </form>

        <form id="form-promocion" class="formulario" action="catalogo.php" method="post" hidden novalidate>
            <p id="error-promocion" class="mensaje-error" role="alert" hidden></p>
            <input type="hidden" name="tipo" value="promocion">
            <input type="hidden" name="id" id="promocion-id">
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
                <button type="submit" class="btn btn-solid" id="btn-guardar-promocion">Agregar promoción</button>
            </div>
        </form>
    </dialog>

    <dialog class="modal-catalogo" id="modal-eliminar" aria-labelledby="modal-eliminar-titulo">
        <div class="fila-flexible modal-catalogo-header">
            <h2 id="modal-eliminar-titulo">Confirmar eliminación</h2>
            <button type="button" class="btn-cerrar-modal" aria-label="Cerrar">×</button>
        </div>
        <form action="catalogo.php" method="post" class="formulario">
            <input type="hidden" name="tipo" value="eliminar">
            <input type="hidden" name="entidad" id="eliminar-entidad">
            <input type="hidden" name="id" id="eliminar-id">
            <p>¿Seguro que deseas eliminar <strong id="eliminar-nombre"></strong>?</p>
            <div class="fila-flexible modal-acciones">
                <button type="button" class="btn btn-outline btn-cancelar-modal">Cancelar</button>
                <button type="submit" class="btn btn-solid">Eliminar</button>
            </div>
        </form>
    </dialog>
    <script src="../js/catalogo.js"></script>
    <script src="../js/validacion.js"></script>
</body>
</html>
