const CORREO_VALIDO = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

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
        if (!nombre || !correo || !password || !confirmar) {
            mensaje = "Completa todos los campos.";
        } else if (!CORREO_VALIDO.test(correo)) {
            mensaje = "Escribe un correo electrónico válido.";
        } else if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/\d/.test(password)) {
            mensaje = "La contraseña debe tener al menos 8 caracteres, una letra y un número.";
        } else if (password !== confirmar) {
            mensaje = "Las contraseñas no coinciden.";
        } else if (!terminos) {
            mensaje = "Debes aceptar los términos y condiciones para continuar.";
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
        } else if (!CORREO_VALIDO.test(correo)) {
            mensaje = "Escribe un correo electrónico válido.";
        }

        if (mensaje) {
            evento.preventDefault();
            mostrarError("#error-login", mensaje);
        }
    });
}

const formBebida = document.querySelector("#form-bebida");
if (formBebida) {
    formBebida.addEventListener("submit", (evento) => {
        const nombre = document.querySelector("#bebida-nombre").value.trim();
        const descripcion = document.querySelector("#bebida-descripcion").value.trim();
        const precio = document.querySelector("#bebida-precio").value.trim();

        let mensaje = "";
        if (!nombre || !descripcion || !precio) {
            mensaje = "Completa todos los campos.";
        } else if (isNaN(Number(precio)) || Number(precio) <= 0) {
            mensaje = "Ingresa un precio válido, mayor a 0.";
        }

        if (mensaje) {
            evento.preventDefault();
            mostrarError("#error-bebida", mensaje);
        }
    });
}

const formPromocion = document.querySelector("#form-promocion");
if (formPromocion) {
    formPromocion.addEventListener("submit", (evento) => {
        const titulo = document.querySelector("#promocion-titulo").value.trim();
        const inicio = document.querySelector("#promocion-inicio").value;
        const vencimiento = document.querySelector("#promocion-vencimiento").value;

        let mensaje = "";
        if (!titulo || !inicio || !vencimiento) {
            mensaje = "Completa todos los campos.";
        } else if (inicio > vencimiento) {
            mensaje = "La fecha de inicio no puede ser mayor que la de vencimiento.";
        }

        if (mensaje) {
            evento.preventDefault();
            mostrarError("#error-promocion", mensaje);
        }
    });
}
