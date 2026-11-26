<?php
    require_once __DIR__ . '/includes/funciones.php';
    $usuario = obtener_usuario_sesion();
    if (!$usuario) {
        header('Location: login.php');
        exit;
    }
    if ($usuario['rol'] !== 'cliente') {
        header('Location: admin/catalogo.php');
        exit;
    }
    $cliente = obtener_datos_cliente($usuario['id_usuario']);
    if (!$cliente) {
        $_SESSION = array();
        session_destroy();
        header('Location: login.php');
        exit;
    }
    $resultado_promociones = obtener_promociones_vigentes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recompensas | Homie Coffee Crew</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php renderizar_navbar(); ?>

    <main id="promos" class="contenedor">
        <?php $sellos = normalizar_sellos($cliente['sellos_actuales']); ?>
        <section class="tarjeta-digital">
            <div id="tarjeta-header" class="fila-flexible">
                <h2>Tarjeta digital</h2>
                <span class="codigo-miembro"><?= escapar(formatear_codigo_miembro($cliente['id_usuario'])) ?></span>
            </div>
            <div class="grid-sellos">
                <?php for ($i = 0; $i < 9; $i++): ?>
                    <?php if ($i < $sellos): ?>
                        <div class="sello completado">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                    <?php else: ?>
                        <div class="sello pendiente"></div>
                    <?php endif; ?>
                <?php endfor; ?>
                <div class="sello premio<?= $sellos >= 10 ? ' listo' : '' ?>">
                    <img src="img/trofeo.png" alt="Premio al completar 10 sellos">
                </div>
            </div>
        </section>

        <section class="promociones-seccion">
            <h1>Promociones de temporada</h1>
            <?php if (!$resultado_promociones): ?>
                <p class="mensaje-error" role="alert">No fue posible cargar las promociones. Verifica la conexión con la base de datos.</p>
            <?php else: ?>
                <div class="fila-flexible">
                    <?php $hay_promociones = false; ?>
                    <?php while ($promocion = mysqli_fetch_assoc($resultado_promociones)): ?>
                        <?php $hay_promociones = true; ?>
                        <article class="card-promo">
                            <h3><?= escapar($promocion['titulo']) ?></h3>
                            <img src="<?= escapar($promocion['imagen_url'] ?: 'img/dosCafes.png') ?>" alt="<?= escapar($promocion['titulo']) ?>">
                            <?php if ((string) $promocion['descripcion'] !== ''): ?>
                                <p><?= escapar($promocion['descripcion']) ?></p>
                            <?php endif; ?>
                            <p class="vigencia">Válido hasta el <?= escapar(date('d/m/Y', strtotime($promocion['fecha_vigencia']))) ?></p>
                        </article>
                    <?php endwhile; ?>
                    <?php if (!$hay_promociones): ?>
                        <p>No hay promociones vigentes por el momento.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="fila-flexible">
            <p><strong>Horario</strong></p>
            <p>Martes-Domingo 10:00 - 20:00 hrs</p>
        </div>
        <div class="redes">
            <a href="https://instagram.com/homie.coffeecrew" target="_blank" rel="noopener noreferrer">
                <img src="img/insta.png" alt="Instagram">
                <p>homie.coffeecrew</p>
            </a>
        </div>
    </footer>
</body>
</html>