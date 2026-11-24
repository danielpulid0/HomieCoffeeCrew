<?php
require_once __DIR__ . '/includes/funciones.php';
iniciar_sesion();

$resultado_bebidas = obtener_bebidas_disponibles();
$bebidas_por_categoria = array();
if ($resultado_bebidas) {
    while ($bebida = mysqli_fetch_assoc($resultado_bebidas)) {
        $bebidas_por_categoria[$bebida['categoria']][] = $bebida;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú | Homie Coffee Crew</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php renderizar_navbar(); ?>

    <main id="menu">
        <section id="menu-header" class="columna-de-elementos">
            <h1>Nuestra Barra de Bebidas</h1>
            <?php if ($resultado_bebidas): ?>
                <div class="filtros-pills">
                    <button class="pill activo" type="button" data-categoria="todas">Todas</button>
                    <?php foreach (array_keys($bebidas_por_categoria) as $categoria): ?>
                        <button class="pill" type="button" data-categoria="<?= escapar($categoria) ?>"><?= escapar($categoria) ?></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php if (!$resultado_bebidas): ?>
            <p class="mensaje-error" role="alert">No fue posible cargar el menú. Verifica la conexión con la base de datos.</p>
        <?php elseif (!$bebidas_por_categoria): ?>
            <p>No hay bebidas disponibles por el momento.</p>
        <?php else: ?>
            <?php foreach ($bebidas_por_categoria as $categoria => $bebidas): ?>
                <section class="categoria-seccion" data-categoria="<?= escapar($categoria) ?>">
                    <h2><?= escapar($categoria) ?></h2>
                    <div class="grid-bebidas">
                        <?php foreach ($bebidas as $bebida): ?>
                            <article class="card-bebida">
                                <img src="<?= escapar($bebida['imagen_url'] ?: 'img/cafeMenu.png') ?>" alt="<?= escapar($bebida['nombre']) ?>">
                                <div class="info-bebida">
                                    <div class="fila-flexible">
                                        <h3><?= escapar($bebida['nombre']) ?></h3>
                                        <span class="precio">$<?= number_format((float) $bebida['precio'], 2) ?></span>
                                    </div>
                                    <?php if ((string) $bebida['descripcion'] !== ''): ?>
                                        <p class="descripcion"><?= escapar($bebida['descripcion']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>

        <section id="personaliza-box">
            <h2>Personaliza tu Bebida</h2>
            <p>Añade notas únicas de sabor artesanal preparadas desde cero en nuestra cocina.</p>
            
            <div class="fila-flexible">
                <div class="card-extra">
                    <div class="extra-header">
                        <h3>Jarabes</h3>
                        <span class="badge-precio">+15</span>
                    </div>
                    <ul class="lista-dos-columnas">
                        <li>Rol de canela</li>
                        <li>Glaseado de rol</li>
                        <li>Plátano</li>
                        <li>Maple salado</li>
                        <li>Vainilla</li>
                        <li>Mocha</li>
                        <li>Caramelo</li>
                    </ul>
                </div>

                <div class="card-extra">
                    <div class="extra-header">
                        <h3>Foamies</h3>
                        <span class="badge-precio">+20</span>
                    </div>
                    <ul class="lista-dos-columnas">
                        <li>Vainilla</li>
                        <li>Maple salado</li>
                        <li>Miel con canela</li>
                        <li>Mocha</li>
                        <li>Caramelo</li>
                        <li>Roll de canela</li>
                        <li>Plátano</li>
                        <li>Matcha</li>
                    </ul>
                </div>

                <div class="card-extra">
                    <div class="extra-header">
                        <h3>Leche Vegetal</h3>
                        <span class="badge-precio">+20</span>
                    </div>
                    <ul class="lista-simple">
                        <li>Opción libre de lácteos con textura cremosa. Leche de avena.</li>
                    </ul>
                </div>
            </div>
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
    <script src="js/menu.js"></script>
</body>
</html>