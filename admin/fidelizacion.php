<?php
require_once __DIR__ . '/../includes/funciones.php';
requerir_empleado();
$resultado_clientes = obtener_clientes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Fidelización | Homie Coffee Crew</title>
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
                <a href="fidelizacion.php" class="activo">Fidelización</a>
                <a href="catalogo.php">Catálogo</a>
                <a href="reportes.php">Reportes y Métricas</a>
            </div>
            <div class="auth-buttons">
                <a href="../login.php?cerrar_sesion=1" aria-label="Cerrar sesión" title="Cerrar sesión">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            </div>
        </nav>
    </header>

    <main id="fidelizacion" class="contenedor">
        <h1>Control de fidelización</h1>
        <section class="panel-admin">
            <div class="buscador-input">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="search" id="buscar-cliente" placeholder="Buscar" aria-label="Buscar cliente">
            </div>

            <div class="columna-de-elementos">
                <?php if (!$resultado_clientes): ?>
                    <p class="mensaje-error" role="alert">No fue posible cargar los clientes. Verifica la conexión con la base de datos.</p>
                <?php elseif (mysqli_num_rows($resultado_clientes) === 0): ?>
                    <p>No hay clientes registrados.</p>
                <?php else: ?>
                    <?php while ($cliente = mysqli_fetch_assoc($resultado_clientes)): ?>
                        <?php $sellos = normalizar_sellos($cliente['sellos_actuales']); ?>
                        <article class="card-cliente">
                            <div class="fila-flexible">
                                <div class="fila-flexible">
                                    <span class="avatar-inicial"><?= escapar(mb_strtoupper(mb_substr($cliente['nombre'], 0, 1, 'UTF-8'), 'UTF-8')) ?></span>
                                    <div>
                                        <h3><?= escapar($cliente['nombre']) ?></h3>
                                        <span class="codigo-cliente"><?= escapar(formatear_codigo_miembro($cliente['id_usuario'])) ?></span>
                                    </div>
                                </div>
                                <div class="cliente-acciones">
                                    <button type="button" class="btn btn-solid">Registrar(+1)</button>
                                    <button type="button" class="btn btn-canjear<?= $sellos >= 10 ? ' disponible' : '' ?>"<?= $sellos < 10 ? ' disabled' : '' ?>>Canjear premio</button>
                                </div>
                            </div>

                            <hr>

                            <div class="cliente-progreso">
                                <span class="label-progreso">Progreso actual:</span>
                                <div class="fila-flexible">
                                    <?php for ($i = 0; $i < 9; $i++): ?>
                                        <span class="sello-mini<?= $i < $sellos ? ' activo' : '' ?>">
                                            <?php if ($i < $sellos): ?>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            <?php endif; ?>
                                        </span>
                                    <?php endfor; ?>
                                    <span class="sello-mini premio<?= $sellos >= 10 ? ' listo' : '' ?>"><img src="../img/trofeo.png" alt="Premio"></span>
                                </div>
                                <span class="conteo-sellos"><?= $sellos ?>/10</span>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
