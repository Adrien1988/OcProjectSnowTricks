document.addEventListener("DOMContentLoaded", function () {
    const scrollDownButton = document.getElementById("scrollDownButton");
    const scrollUpButton = document.getElementById("scrollUpButton");
    const figuresList = document.getElementById("figures-list");

    // Le bouton "Retour en haut" est caché au début
    scrollUpButton.style.display = "none";

    // Lors du clic sur le bouton "Aller vers le bas"
    scrollDownButton.addEventListener("click", function () {
        // Scroller jusqu'au conteneur "figures-list"
        window.scrollTo({
            top: figuresList.offsetTop,
            behavior: "smooth"
        });

        // Cacher le bouton "Aller vers le bas"
        scrollDownButton.style.display = "none";
        // Afficher le bouton "Retour en haut"
        scrollUpButton.style.display = "block";
    });

    // Lors du clic sur le bouton "Retour en haut"
    scrollUpButton.addEventListener("click", function () {
        // Faire défiler la page vers le haut
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

        // Cacher "Retour en haut"
        scrollUpButton.style.display = "none";
        // Réafficher "Aller vers le bas"
        scrollDownButton.style.display = "block";
    });

    // Afficher/Cacher les boutons en fonction du défilement
    window.addEventListener("scroll", function () {
        if (window.scrollY > 200) {
            // Une fois qu'on a défilé un peu (200px), on cache "Aller vers le bas" et on montre "Retour en haut"
            scrollDownButton.style.display = "none";
            scrollUpButton.style.display = "block";
        } else {
            // Sinon, on affiche "Aller vers le bas" et on cache "Retour en haut"
            scrollDownButton.style.display = "block";
            scrollUpButton.style.display = "none";
        }
    });
});
