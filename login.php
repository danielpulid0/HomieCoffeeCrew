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
$mensaje = isset($_SESSION['mensaje_login']) ? $_SESSION['mensaje_login'] : '';
unset($_SESSION['mensaje_login']);

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = isset($_POST['correo']) && is_string($_POST['correo']) ? trim($_POST['correo']) : '';
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Escribe un correo electrónico y una contraseña válidos.';
    } else {
        $usuario = autenticar_usuario($correo, $password);
        if (is_array($usuario)) {
            session_regenerate_id(true);
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
    <link rel="stylesheet" href="css/styles2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="fondo-login">
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

    <main id="login">
        <div class="card-login columna-de-elementos">
            <img src="img/homie_logo.png" alt="Homie Logo">
            <div class="titulo-subtitulo texto-centrado">
                <h1>Bienvenido, Homie</h1>
                <p>Inicia sesión en tu cuenta</p>
            </div>
            <form action="login.php" method="POST" class="formulario">
                <?php if ($mensaje !== ''): ?>
                    <p class="mensaje-exito" role="status"><?= escapar($mensaje) ?></p>
                <?php endif; ?>
                <?php if ($error !== ''): ?>
                    <p class="mensaje-error" role="alert"><?= escapar($error) ?></p>
                <?php endif; ?>
                <div class="grupo-input">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?= escapar($correo) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="password">Contraseña:</label>
                    <div class="input-password">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="btn-ojo" aria-label="Mostrar contraseña">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <span class="link-olvido">¿Olvidaste tu contraseña?</span>
                </div>

                <button type="submit" class="btn btn-solid btn-block">Ingresar</button>
            </form>

            <p class="pie-form">¿No tienes cuenta? <a href="cliente/registro.php">Regístrate</a></p>
        </div>
    </main>
</body>
</html>