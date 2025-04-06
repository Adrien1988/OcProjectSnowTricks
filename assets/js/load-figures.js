document.addEventListener('DOMContentLoaded', () => {
    const loadMoreBtn = document.getElementById('load-more');
    const container = document.getElementById('figures-container');

    if (!loadMoreBtn || !container) return;

    loadMoreBtn.addEventListener('click', async () => {
        const offset = parseInt(loadMoreBtn.getAttribute('data-offset')) || 0;
        loadMoreBtn.disabled = true;
        loadMoreBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Chargement...`;

        try {
            const response = await fetch(`/load-more-figures?offset=${offset}`);
            const data = await response.json();

            // Injecter le HTML
            container.insertAdjacentHTML('beforeend', data.html);

            if (data.hasMore) {
                loadMoreBtn.disabled = false;
                loadMoreBtn.innerHTML = 'Load more';
                loadMoreBtn.setAttribute('data-offset', offset + 15);
            } else {
                loadMoreBtn.innerHTML = "Plus de figures à afficher";
                loadMoreBtn.classList.add('btn-secondary');
            }

        } catch (err) {
            console.error("Erreur AJAX :", err);
            loadMoreBtn.disabled = false;
            loadMoreBtn.innerHTML = "Réessayer";
        }
    });
});
