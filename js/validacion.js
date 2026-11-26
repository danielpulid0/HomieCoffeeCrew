function mostrarError(elementoError, mensaje) {
    if (!elementoError) return;
    elementoError.textContent = mensaje;
    elementoError.hidden = false;
}

const formRegistro = document.querySelector("#form-registro");
if (formRegistro) {
    const errorRegistro = document.querySelector("#error-registro");

    formRegistro.addEventListener("submit", (evento) => {
        const nombre = document.querySelector("#nombre").value.trim();
        const correo = document.querySelector("#correo").value.trim();
        const password = document.querySelector("#password").value;
        const confirmar = document.querySelector("#confirm_password").value;
        const terminos = document.querySelector('input[name="terminos"]').checked;

        let mensaje = "";

        if (nombre === "") {
            mensaje = "Escribe tu nombre.";
        } else if (correo === "" || !correo.includes("@") || !correo.includes(".")) {
            mensaje = "Escribe un correo electrónico válido.";
        } else if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/\d/.test(password)) {
            mensaje = "La contraseña debe tener al menos 8 caracteres, una letra y un número.";
        } else if (password !== confirmar) {
            mensaje = "Las contraseñas no coinciden.";
        } else if (!terminos) {
            mensaje = "Debes aceptar los términos y condiciones para continuar.";
        }

        if (mensaje !== "") {
            evento.preventDefault();
            mostrarError(errorRegistro, mensaje);
        } else if (errorRegistro) {
            errorRegistro.hidden = true;
        }
    });
}

const formLogin = document.querySelector("#form-login");
if (formLogin) {
    const errorLogin = document.querySelector("#error-login");

    formLogin.addEventListener("submit", (evento) => {
        const correo = document.querySelector("#correo").value.trim();
        const password = document.querySelector("#password").value;

        let mensaje = "";

        if (correo === "" || !correo.includes("@")) {
            mensaje = "Ingresa un correo electrónico válido.";
        } else if (password === "") {
            mensaje = "Ingresa tu contraseña.";
        }

        if (mensaje !== "") {
            evento.preventDefault();
            mostrarError(errorLogin, mensaje);
        } else if (errorLogin) {
            errorLogin.hidden = true;
        }
    });
}
