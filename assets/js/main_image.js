document.addEventListener("DOMContentLoaded", function () {

    function highlightSelected(img) {
        document.querySelectorAll('.img-thumbnail').forEach(el => el.classList.remove('border-primary'));
        img.classList.add('border-primary');
    }

    // ✅ Mise à jour de la gestion de la sélection d’image principale
    const imageLabels = document.querySelectorAll(".image-label");

    imageLabels.forEach(label => {
        label.addEventListener("click", function () {
            const radioInput = this.querySelector("input[type='radio']");
            const img = this.querySelector("img");

            if (radioInput && img) {
                radioInput.checked = true; // Sélectionner le radio input
                highlightSelected(img); // Ajouter la bordure à l’image sélectionnée
            }
        });
    });

    // ✅ Gestion de la suppression de l'image principale
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
                    
                    // ✅ Remplace immédiatement l’image principale par l’image par défaut
                    const mainImageElement = document.querySelector("#mainImage");
                    if (mainImageElement) {
                        mainImageElement.src = "/build/images/default-image.jpg";
                    }

                    // ✅ Ferme correctement la modale de suppression
                    let modalElement = document.querySelector("#deleteMainImageModal");
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(error => console.error("Erreur lors de la suppression de l'image principale :", error));
        });
    }
});
