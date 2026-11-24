const botonesFiltro = document.querySelectorAll(".filtros-pills .pill");
const seccionesCategoria = document.querySelectorAll(".categoria-seccion");

botonesFiltro.forEach((boton) => {
    boton.addEventListener("click", () => {
        const categoriaSeleccionada = boton.dataset.categoria;

        botonesFiltro.forEach((pill) => pill.classList.remove("activo"));
        boton.classList.add("activo");

        seccionesCategoria.forEach((seccion) => {
            const mostrar = categoriaSeleccionada === "todas" || seccion.dataset.categoria === categoriaSeleccionada;
            seccion.classList.toggle("oculto", !mostrar);
        });
    });
});
