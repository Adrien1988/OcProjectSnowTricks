document.addEventListener("DOMContentLoaded", function () {
    console.log("main_image.js chargé !"); // Debugging pour vérifier si le fichier est bien inclus

    function highlightSelected(img) {
        console.log("highlightSelected appelé !"); // Vérification si la fonction est bien déclenchée
        document.querySelectorAll('.img-thumbnail').forEach(el => el.classList.remove('border-primary'));
        img.classList.add('border-primary');
    }

    // Sélection des images pour la mise en surbrillance
    const imageLabels = document.querySelectorAll(".image-label");

    imageLabels.forEach(label => {
        label.addEventListener("click", function () {
            const img = this.querySelector("img");
            highlightSelected(img);
            this.querySelector("input[type='radio']").checked = true;
        });
    });

    // Gestion de la suppression de l'image principale
    const deleteMainImageForm = document.querySelector("#deleteMainImageForm");

    if (deleteMainImageForm) {
        deleteMainImageForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Empêche la soumission classique du formulaire

            const formData = new FormData(this);
            const actionUrl = this.getAttribute("action");

            fetch(actionUrl, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Image principale supprimée avec succès !");
                    // Remplace l'image principale par l'image par défaut
                    const mainImageElement = document.querySelector("#mainImage");
                    if (mainImageElement) {
                        mainImageElement.src = "/build/images/default-image.jpg";
                    }

                    // Ferme la modale de suppression
                    let modal = bootstrap.Modal.getInstance(document.querySelector("#deleteMainImageModal"));
                    modal.hide();
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(error => console.error("Erreur lors de la suppression de l'image principale :", error));
        });
    }
});
