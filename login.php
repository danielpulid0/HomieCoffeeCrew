<?php
require_once __DIR__ . '/includes/funciones.php';
iniciar_sesion();

if (isset($_GET['cerrar_sesion'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: login.php');
    exit;
}

$error = '';
$correo = '';
$mensaje = $_SESSION['mensaje_login'] ?? '';
unset($_SESSION['mensaje_login']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = obtener_post_texto('correo');
    $password = obtener_post_texto('password', false);

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Escribe un correo electrónico y una contraseña válidos.';
    } else {
        $usuario = autenticar_usuario($correo, $password);
        if (is_array($usuario)) {
            $_SESSION['usuario'] = $usuario;
            header('Location: ' . ($usuario['rol'] === 'cliente' ? 'recompensas.php' : 'admin/catalogo.php'));
            exit;
        }

        $error = $usuario === null
            ? 'El correo o la contraseña no son correctos.'
            : 'No se pudo iniciar sesión. Verifica la conexión con la base de datos e inténtalo de nuevo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Homie Coffee Crew</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="fondo-login">
    <?php renderizar_navbar(); ?>

    <div id="login">
        <div class="card-login columna-de-elementos">
            <img src="img/homie_logo.png" alt="Homie Logo">
            <div class="titulo-subtitulo texto-centrado">
                <h1>Bienvenido, Homie</h1>
                <p>Inicia sesión en tu cuenta</p>
            </div>
            <form id="form-login" action="login.php" method="POST" class="formulario" novalidate>
                <?php if ($mensaje !== ''): ?>
                    <p class="mensaje-exito" role="status"><?= escapar($mensaje) ?></p>
                <?php endif; ?>
                <p id="error-login" class="mensaje-error" role="alert"<?= $error === '' ? ' hidden' : '' ?>><?= escapar($error) ?></p>
                <div class="grupo-input">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?= escapar($correo) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="password">Contraseña:</label>
                    <div class="input-password">
                        <input type="password" id="password" name="password" required>
                    </div>
                    <p class="link-olvido">¿Olvidaste tu contraseña?</p>
                </div>

                <button type="submit" class="btn btn-solid btn-block">Ingresar</button>
            </form>

            <p class="pie-form">¿No tienes cuenta? <a href="cliente/registro.php">Regístrate</a></p>
        </div>
    </div>
    <script src="js/validacion.js"></script>
</body>
</html>