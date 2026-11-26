<?php
require_once __DIR__ . '/../includes/funciones.php';
iniciar_sesion();

$error = '';
$nombre = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = obtener_post_texto('nombre');
    $correo = obtener_post_texto('correo');
    $password = obtener_post_texto('password', false);
    $confirmar = obtener_post_texto('confirm_password', false);

    if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Escribe un nombre y un correo electrónico válido.';
    } elseif (strlen($password) < 8 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password)) {
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
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="fondo-crema">
    <?php renderizar_navbar('../'); ?>

    <main id="registro" class="dos-columnas">
        <img src="../img/cafeMano.png" alt="Vaso Homie Coffee Crew en mano">
        <div class="form-wrapper contenedor columna-de-elementos">
            <h1>Registro</h1>
            <form id="form-registro" action="registro.php" method="POST" class="formulario columna-de-elementos">
                <?php if ($error !== ''): ?>
                    <p class="mensaje-error" role="alert"><?= escapar($error) ?></p>
                <?php endif; ?>
                <p id="error-registro" class="mensaje-error" role="alert" hidden></p>
                <div class="grupo-input columna-de-elementos">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" value="<?= escapar($nombre) ?>" required>
                </div>

                <div class="grupo-input columna-de-elementos">
                    <label for="correo">Correo electrónico *</label>
                    <input type="email" id="correo" name="correo" maxlength="120" value="<?= escapar($correo) ?>" required>
                </div>

                <div class="grupo-input columna-de-elementos">
                    <label for="password">Contraseña *</label>
                    <div class="input-password">
                        <input type="password" id="password" name="password" required>
                    </div>
                    <ul class="requisitos-pass">
                        <li>Mínimo 8 caracteres</li>
                        <li>Al menos una letra</li>
                        <li>Al menos un número</li>
                    </ul>
                </div>

                <div class="grupo-input columna-de-elementos">
                    <label for="confirm_password">Confirmar contraseña *</label>
                    <div class="input-password">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>

                <label class="caja-terminos">
                    <input type="checkbox" name="terminos" required>
                    <span>Acepto los <strong>términos y condiciones</strong> y la <strong>política de privacidad</strong>.</span>
                </label>

                <button type="submit" class="btn btn-solid btn-block">Crear cuenta</button>
                <p class="pie-form texto-centrado">¿Ya tienes cuenta? <a href="../login.php">Inicia sesión</a></p>
            </form>

        </div>
    </main>
    <script src="../js/validacion.js"></script>
</body>
</html>