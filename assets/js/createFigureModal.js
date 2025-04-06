document.addEventListener('DOMContentLoaded', () => {
    function bindCreateFigureForm() {
        const form = document.getElementById('create-figure-form');
        if (!form) return;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            const submitButton = document.getElementById('create-figure-submit-button');
            const spinner = submitButton?.querySelector('.spinner-border');

            if (submitButton && spinner) {
                submitButton.disabled = true;
                spinner.classList.remove('d-none');
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newFormWrapper = doc.querySelector('#create-figure-form-wrapper');
                const currentFormWrapper = document.querySelector('#create-figure-form-wrapper');

                const redirect = doc.querySelector('#create-figure-success');
                if (redirect) {
                    window.location.href = redirect.dataset.redirect;
                    return;
                }

                if (submitButton && spinner) {
                    submitButton.disabled = false;
                    spinner.classList.add('d-none');
                }

                if (newFormWrapper && currentFormWrapper) {
                    // Animation fade-out
                    currentFormWrapper.style.opacity = 0;

                    setTimeout(() => {
                        // Remplace le contenu de la modale
                        currentFormWrapper.innerHTML = newFormWrapper.innerHTML;
                        currentFormWrapper.style.opacity = 1;

                        // Rebind le nouveau formulaire
                        bindCreateFigureForm();

                        // Focus auto sur le champ avec erreur (si présent)
                        requestAnimationFrame(() => {
                            const nameInput = document.querySelector('#create-figure-form input[name="figure[name]"]');
                            if (nameInput && nameInput.classList.contains('is-invalid')) {
                                nameInput.focus();
                            }
                        });
                    }, 150); // durée pour l'effet de transition
                }
            } catch (error) {
                console.error('Erreur lors de la soumission AJAX :', error);
                // Réactive les éléments si erreur
                if (submitButton && spinner) {
                    submitButton.disabled = false;
                    spinner.classList.add('d-none');
                }
            }
        });
    }

    bindCreateFigureForm(); // premier bind
});
