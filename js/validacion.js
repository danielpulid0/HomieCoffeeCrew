function mostrarError(selector, mensaje) {
    const elemento = document.querySelector(selector);
    if (!elemento) return;
    elemento.textContent = mensaje;
    elemento.hidden = false;
}

const formRegistro = document.querySelector("#form-registro");
if (formRegistro) {
    formRegistro.addEventListener("submit", (evento) => {
        const nombre = document.querySelector("#nombre").value.trim();
        const correo = document.querySelector("#correo").value.trim();
        const password = document.querySelector("#password").value;
        const confirmar = document.querySelector("#confirm_password").value;
        const terminos = document.querySelector('input[name="terminos"]').checked;

        let mensaje = "";
        if (!nombre || !correo || !password || !confirmar || !terminos) {
            mensaje = "Completa todos los campos.";
        } else if (!correo.includes("@") || !correo.includes(".")) {
            mensaje = "Escribe un correo electrónico válido.";
        } else if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/\d/.test(password)) {
            mensaje = "La contraseña debe tener al menos 8 caracteres, una letra y un número.";
        } else if (password !== confirmar) {
            mensaje = "Las contraseñas no coinciden.";
        }

        if (mensaje) {
            evento.preventDefault();
            mostrarError("#error-registro", mensaje);
        }
    });
}

const formLogin = document.querySelector("#form-login");
if (formLogin) {
    formLogin.addEventListener("submit", (evento) => {
        const correo = document.querySelector("#correo").value.trim();
        const password = document.querySelector("#password").value;

        let mensaje = "";
        if (!correo || !password) {
            mensaje = "Completa todos los campos.";
        } else if (!correo.includes("@")) {
            mensaje = "Escribe un correo electrónico válido.";
        }

        if (mensaje) {
            evento.preventDefault();
            mostrarError("#error-login", mensaje);
        }
    });
}
