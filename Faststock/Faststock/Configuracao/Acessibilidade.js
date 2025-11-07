    const btn = document.getElementById("btnAcessibilidade");
    const menu = document.getElementById("menuAcessibilidade");

    btn.addEventListener("click", () => {
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    });

    function toggleDaltonismo() {
        document.body.classList.toggle("daltonismo");
    }

    function toggleFonte() {
        document.body.classList.toggle("fonte-grande");
    }

    function toggleContraste() {
        document.body.classList.toggle("contraste");
    }

