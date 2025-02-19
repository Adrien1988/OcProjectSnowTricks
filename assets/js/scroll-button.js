document.addEventListener("DOMContentLoaded", function () {
    const scrollDownButton = document.getElementById("scrollDownButton");
    const scrollUpButton = document.getElementById("scrollUpButton");

    // Afficher le bouton retour en haut seulement quand l'utilisateur a défilé vers le bas
    scrollUpButton.style.display = "none";  // Le bouton retour en haut est caché au début

    // Lors du clic sur le bouton vers le bas
    scrollDownButton.addEventListener("click", function () {
        // Faire défiler la page vers le bas avec une animation fluide
        window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });

        // Cacher le bouton "Aller vers le bas" une fois cliqué
        scrollDownButton.style.display = "none";

        // Afficher le bouton "Retour en haut"
        scrollUpButton.style.display = "block";
    });

    // Lors du clic sur le bouton retour en haut
    scrollUpButton.addEventListener("click", function () {
        // Faire défiler la page vers le haut avec une animation fluide
        window.scrollTo({ top: 0, behavior: "smooth" });

        // Cacher le bouton "Retour en haut" une fois cliqué
        scrollUpButton.style.display = "none";

        // Réafficher le bouton "Aller vers le bas"
        scrollDownButton.style.display = "block";
    });

    // Afficher le bouton retour en haut quand on défile vers le bas
    window.addEventListener("scroll", function () {
        if (window.scrollY > 200) {
            scrollDownButton.style.display = "none";  // Cacher "Aller vers le bas"
            scrollUpButton.style.display = "block";   // Afficher "Retour en haut"
        } else {
            scrollDownButton.style.display = "block"; // Afficher "Aller vers le bas"
            scrollUpButton.style.display = "none";    // Cacher "Retour en haut"
        }
    });
});
