<?php
require_once __DIR__ . '/../includes/funciones.php';
iniciar_sesion();

$error = '';
$nombre = '';
$correo = '';

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) && is_string($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $correo = isset($_POST['correo']) && is_string($_POST['correo']) ? trim($_POST['correo']) : '';
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $confirmar = isset($_POST['confirm_password']) && is_string($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 100 || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 120) {
        $error = 'Escribe un nombre y un correo electrónico válido.';
    } elseif (mb_strlen($password, 'UTF-8') < 8 || !preg_match('/\p{L}/u', $password) || !preg_match('/\d/', $password)) {
        $error = 'La contraseña debe tener al menos 8 caracteres, una letra y un número.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!isset($_POST['terminos'])) {
        $error = 'Debes aceptar los términos y condiciones para continuar.';
    } else {
        $registro = registrar_cliente($nombre, $correo, $password);
        if ($registro === true) {
            $_SESSION['mensaje_login'] = 'Tu cuenta se creó correctamente. Ya puedes iniciar sesión.';
            header('Location: ../login.php');
            exit;
        }

        $error = $registro === 'correo_existente'
            ? 'Ya existe una cuenta con ese correo electrónico.'
            : 'No se pudo crear la cuenta. Verifica la conexión con la base de datos e inténtalo de nuevo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Homie Coffee Crew</title>
    <link rel="stylesheet" href="../css/styles2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="fondo-crema">
    <header>
        <nav>
            <a href="../index.php" class="nombre-y-logo">
                <img src="../img/homie_logo.png" alt="Logo Homie Coffee Crew">
                <p class="nombre-marca">Homie Coffee Crew</p>
            </a>
            <div class="menu-links">
                <a href="../menu.php">Menú</a>
                <a href="../recompensas.php">Recompensas</a>
            </div>
            <div class="auth-buttons">
                <a href="../login.php" class="btn btn-outline btn">Iniciar sesión</a>
                <a href="registro.php" class="btn btn-solid btn">Unirse al crew</a>
            </div>
        </nav>
    </header>

    <main id="registro" class="dos-columnas">
        <img src="../img/cafeMano.png" alt="Vaso Homie Coffee Crew en mano">
        <div class="form-wrapper">
            <h1>Registro</h1>
            <form action="registro.php" method="POST" class="formulario columna-de-elementos">
                <?php if ($error !== ''): ?>
                    <p class="mensaje-error" role="alert"><?= escapar($error) ?></p>
                <?php endif; ?>
                <div class="grupo-input">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" value="<?= escapar($nombre) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="correo">Correo electrónico *</label>
                    <input type="email" id="correo" name="correo" maxlength="120" value="<?= escapar($correo) ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="password">Contraseña *</label>
                    <div class="input-password">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="btn-ojo" aria-label="Mostrar contraseña">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <ul class="requisitos-pass">
                        <li>Mínimo 8 caracteres</li>
                        <li>Al menos una letra</li>
                        <li>Al menos un número</li>
                    </ul>
                </div>

                <div class="grupo-input">
                    <label for="confirm_password">Confirmar contraseña *</label>
                    <div class="input-password">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="btn-ojo" aria-label="Mostrar contraseña">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <label class="caja-terminos">
                    <input type="checkbox" name="terminos" required>
                    <span>Acepto los <strong>términos y condiciones</strong> y la <strong>política de privacidad</strong>.</span>
                </label>

                <button type="submit" class="btn btn-solid btn-block">Crear cuenta</button>
            </form>

            <p class="pie-form">¿Ya tienes cuenta? <a href="../login.php">Inicia sesión</a></p>
        </div>
    </main>
</body>
</html>