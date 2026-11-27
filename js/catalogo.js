const modalItem = document.querySelector("#modal-item");
const modalTitulo = document.querySelector("#modal-titulo");
const modalEliminar = document.querySelector("#modal-eliminar");
const selectorTipoItem = document.querySelector("#selector-tipo-item");
const formulariosItem = {
    bebida: document.querySelector("#form-bebida"),
    promocion: document.querySelector("#form-promocion")
};

document.querySelector("#abrir-modal-item").addEventListener("click", () => {
    modalItem.showModal();
});

document.querySelectorAll(".modal-catalogo").forEach((modal) => {
    modal.addEventListener("click", (evento) => {
        if (evento.target === modal) modal.close();
    });
    modal.querySelectorAll(".btn-cerrar-modal, .btn-cancelar-modal").forEach((boton) => {
        boton.addEventListener("click", () => modal.close());
    });
});

document.querySelectorAll("[data-editar-tipo]").forEach((boton) => {
    boton.addEventListener("click", () => {
        const tipo = boton.dataset.editarTipo;
        const formulario = formulariosItem[tipo];
        if (!formulario) return;

        selectorTipoItem.hidden = true;
        Object.values(formulariosItem).forEach((form) => { form.hidden = true; });
        formulario.hidden = false;
        formulario.querySelector(".volver-selector").hidden = true;

        if (tipo === "bebida") {
            modalTitulo.textContent = "Editar bebida";
            document.querySelector("#bebida-id").value = boton.dataset.id;
            document.querySelector("#bebida-nombre").value = boton.dataset.nombre;
            document.querySelector("#bebida-categoria").value = boton.dataset.categoria;
            document.querySelector("#bebida-descripcion").value = boton.dataset.descripcion;
            document.querySelector("#bebida-precio").value = boton.dataset.precio;
            document.querySelector("#bebida-estado").value = boton.dataset.estado;
            document.querySelector("#btn-guardar-bebida").textContent = "Guardar cambios";
        } else {
            modalTitulo.textContent = "Editar promoción";
            document.querySelector("#promocion-id").value = boton.dataset.id;
            document.querySelector("#promocion-titulo").value = boton.dataset.titulo;
            document.querySelector("#promocion-inicio").value = boton.dataset.inicio;
            document.querySelector("#promocion-vencimiento").value = boton.dataset.vencimiento;
            document.querySelector("#promocion-estado").value = boton.dataset.estado;
            document.querySelector("#btn-guardar-promocion").textContent = "Guardar cambios";
        }

        modalItem.showModal();
    });
});

document.querySelectorAll("[data-eliminar-id]").forEach((boton) => {
    boton.addEventListener("click", () => {
        document.querySelector("#eliminar-entidad").value = boton.dataset.eliminarEntidad;
        document.querySelector("#eliminar-id").value = boton.dataset.eliminarId;
        document.querySelector("#eliminar-nombre").textContent = boton.dataset.eliminarNombre;
        modalEliminar.showModal();
    });
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
    modalTitulo.textContent = "Agregar item";
    document.querySelector("#bebida-id").value = "";
    document.querySelector("#promocion-id").value = "";
    document.querySelector("#btn-guardar-bebida").textContent = "Agregar bebida";
    document.querySelector("#btn-guardar-promocion").textContent = "Agregar promoción";
    Object.values(formulariosItem).forEach((formulario) => {
        formulario.reset();
        formulario.hidden = true;
        formulario.querySelector(".volver-selector").hidden = false;
        const mensajeError = formulario.querySelector(".mensaje-error");
        if (mensajeError) mensajeError.hidden = true;
    });
    selectorTipoItem.hidden = false;
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
