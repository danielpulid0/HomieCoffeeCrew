<?php
require_once __DIR__ . '/includes/funciones.php';
$resultado_promociones = obtener_promociones_vigentes();
$promocion_destacada = $resultado_promociones
    ? mysqli_fetch_assoc($resultado_promociones)
    : false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homie Coffee Crew</title>
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

    <main>
        <section id="hero" class="dos-columnas">
            <?php if ($promocion_destacada): ?>
                <img src="<?= escapar($promocion_destacada['imagen_url'] ?: 'img/cafeHalloween.png') ?>" alt="<?= escapar($promocion_destacada['titulo']) ?>">
                <div class="columna-de-elementos">
                    <h1><?= escapar($promocion_destacada['titulo']) ?></h1>
                    <h2>Promoción vigente hasta el <?= escapar(date('d/m/Y', strtotime($promocion_destacada['fecha_vigencia']))) ?></h2>
                    <?php if ($promocion_destacada['descripcion'] !== null && $promocion_destacada['descripcion'] !== ''): ?>
                        <p><?= escapar($promocion_destacada['descripcion']) ?></p>
                    <?php endif; ?>
                </div>
            <?php elseif ($resultado_promociones): ?>
                <img src="img/cafeHalloween.png" alt="Bebida de temporada">
                <div class="columna-de-elementos">
                    <h1>Homie Coffee Crew</h1>
                    <h2>Descubre nuestras bebidas.</h2>
                    <a href="menu.php" class="enlace-subrayado">Ver el menú</a>
                </div>
            <?php else: ?>
                <div class="columna-de-elementos">
                    <p class="mensaje-error" role="alert">No fue posible cargar la promoción destacada.</p>
                </div>
            <?php endif; ?>
        </section>

        <section id="reposteria" class="dos-columnas">
            <div class="columna-de-elementos">
                <h2>Porque siempre hay hueco para algo dulce</h2>
                <p>Pregunta por la repostería del día!</p>
            </div>
            <div class="dos-columnas">
                <div class="card-reposteria">
                    <img src="img/panPasas.png" alt="Panqué con pasas">
                </div>
                <div class="card-reposteria">
                    <img src="img/galletas.png" alt="Galletas artesanales">
                </div>
            </div>
        </section>

        <section id="match-dia">
            <div class="fila-flexible texto-centrado">
                <img src="img/cafeFrio.png" alt="Bebida fría" class="vaso-flotante">
                <div class="columna-de-elementos">
                    <h2>Encuentra tu match del día</h2>
                    <a href="menu.php" class="enlace-subrayado">Ver todas las bebidas</a>
                </div>
                <img src="img/cafeCanela.png" alt="Bebida fría con cereal" class="vaso-flotante">
            </div>
        </section>

        <section id="sucursal-wrapper" class="dos-columnas">
            <div class="columna-de-elementos">
                <h2>Visítanos en nuestra sucursal</h2>
                <h3>Guaycura</h3>
                <p>C. Finisterra 6339, Guaycura, 22216 Tijuana, B.C.</p>
            </div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3365.2638909691586!2d-116.92331128844313!3d32.4923752983678!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80d939003496d285%3A0x2981e7c5e6e8bebd!2sHomie%20Coffee%20Crew!5e0!3m2!1ses!2smx!4v1794512915354!5m2!1ses!2smx" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" class="mapa"></iframe>                
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