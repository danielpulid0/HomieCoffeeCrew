<?php
require_once __DIR__ . '/includes/funciones.php';
iniciar_sesion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homie Coffee Crew</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php renderizar_navbar(); ?>

    <main>
        <section id="hero" class="dos-columnas">
            <img src="img/cafeHalloween.png" alt="Bebida de temporada">
            <div class="columna-de-elementos hero-text">
                <h1>¡Solo por tiempo limitado!</h1>
                <h2>Ven a probar nuestros productos de temporada.</h2>
                <p>Aquí nos tomamos el tiempo de elaborar desde cero todos nuestros productos. ¡Ven a probar nuestro sabor artesanal!</p>
            </div>
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
                    <a href="menu.html" class="enlace-subrayado">Ver todas las bebidas</a>
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