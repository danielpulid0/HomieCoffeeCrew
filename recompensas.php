<?php
require_once __DIR__ . '/includes/funciones.php';
iniciar_sesion();

$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
$cliente = $usuario && $usuario['rol'] === 'cliente'
    ? obtener_datos_cliente($usuario['id_usuario'])
    : null;
$resultado_promociones = obtener_promociones_vigentes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recompensas | Homie Coffee Crew</title>
    <link rel="stylesheet" href="css/styles2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="nombre-y-logo">
                <img src="img/homie_logo.png" alt="Logo Homie Coffee Crew">
                <p class="nombre-marca">Homie Coffee Crew</p>
            </a>
            <div class="menu-links">
                <a href="menu.php">Menú</a>
                <a href="recompensas.php">Recompensas</a>
            </div>
            <div class="auth-buttons">
                <a href="login.php" class="btn btn-outline btn">Iniciar sesión</a>
                <a href="cliente/registro.php" class="btn btn-solid btn">Unirse al crew</a>
            </div>
        </nav>
    </header>

    <main id="promos">
        <?php if ($cliente): ?>
            <?php $sellos = max(0, min(10, (int) $cliente['sellos_actuales'])); ?>
            <section class="tarjeta-digital">
                <div id="tarjeta-header" class="fila-flexible">
                    <h2>Tarjeta digital de <?= escapar($cliente['nombre']) ?></h2>
                    <span class="codigo-miembro">HOMMIE-<?= str_pad((string) $cliente['id_usuario'], 3, '0', STR_PAD_LEFT) ?></span>
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
        <?php elseif (!$usuario): ?>
            <p class="mensaje-exito">Inicia sesión para consultar tu tarjeta de sellos.</p>
        <?php else: ?>
            <p class="mensaje-exito">La tarjeta de sellos está disponible para cuentas de cliente.</p>
        <?php endif; ?>

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
                            <?php if ($promocion['descripcion'] !== null && $promocion['descripcion'] !== ''): ?>
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