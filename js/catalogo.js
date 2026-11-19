const modalItem = document.querySelector("#modal-item");
const selectorTipoItem = document.querySelector("#selector-tipo-item");
const formulariosItem = {
    bebida: document.querySelector("#form-bebida"),
    promocion: document.querySelector("#form-promocion")
};

document.querySelector("#abrir-modal-item").addEventListener("click", () => {
    modalItem.showModal();
});

document.querySelector(".btn-cerrar-modal").addEventListener("click", () => {
    modalItem.close();
});

document.querySelectorAll("[data-tipo-item]").forEach((boton) => {
    boton.addEventListener("click", () => {
        const formulario = formulariosItem[boton.dataset.tipoItem];
        if (!formulario) return;

        selectorTipoItem.hidden = true;
        formulario.hidden = false;
        formulario.querySelector("input:not([type=hidden])").focus();
    });
});

document.querySelectorAll(".volver-selector").forEach((boton) => {
    boton.addEventListener("click", () => {
        Object.values(formulariosItem).forEach((formulario) => {
            formulario.hidden = true;
        });
        selectorTipoItem.hidden = false;
    });
});

modalItem.addEventListener("close", () => {
    Object.values(formulariosItem).forEach((formulario) => {
        formulario.reset();
        formulario.hidden = true;
    });
    selectorTipoItem.hidden = false;
});

modalItem.addEventListener("click", (evento) => {
    if (evento.target === modalItem) {
        modalItem.close();
    }
});

document.querySelectorAll(".tab-btn").forEach((boton) => {
    boton.addEventListener("click", () => {
        document.querySelectorAll(".tab-btn").forEach((tab) => tab.classList.remove("activo"));
        boton.classList.add("activo");
        document.querySelectorAll("#tab-bebidas, #tab-promociones").forEach((panel) => {
            panel.classList.toggle("oculto", panel.id !== boton.dataset.tab);
        });
    });
});
