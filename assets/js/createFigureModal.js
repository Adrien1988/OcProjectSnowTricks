document.addEventListener('DOMContentLoaded', () => {
    function bindCreateFigureForm() {
        const form = document.getElementById('create-figure-form');
        if (!form) return;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const html = await response.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Chercher le nouveau contenu du formulaire
            const newFormWrapper = doc.querySelector('#create-figure-form-wrapper');
            const currentFormWrapper = document.querySelector('#create-figure-form-wrapper');

            // Chercher redirection
            const redirect = doc.querySelector('#create-figure-success');
            if (redirect) {
                window.location.href = redirect.dataset.redirect;
                return;
            }

            // Sinon, remplacer le contenu du formulaire dans la modale
            if (newFormWrapper && currentFormWrapper) {
                currentFormWrapper.innerHTML = newFormWrapper.innerHTML;
                bindCreateFigureForm(); // rebinder le submit
            }
        });
    }

    // Initial bind
    bindCreateFigureForm();
});
