document.addEventListener('DOMContentLoaded', () => {
    const loadMoreBtn = document.getElementById('load-more');
    if (!loadMoreBtn) {
      return; // Pas de bouton => rien à faire
    }
  
    loadMoreBtn.addEventListener('click', async function () {
      let offset = parseInt(loadMoreBtn.getAttribute('data-offset'), 10) || 15;
  
      try {
        const response = await fetch('/load-more-figures?offset=' + offset);
        if (!response.ok) {
          console.error('Erreur AJAX', response.status);
          return;
        }
  
        const newHtml = await response.text();
  
        // Si la réponse est vide => plus de figures
        if (newHtml.trim() === '') {
          loadMoreBtn.textContent = "Plus de figures à afficher";
          loadMoreBtn.disabled = true;
          return;
        }
  
        // On insère ce code au bas du container
        const container = document.getElementById('figures-container');
        container.insertAdjacentHTML('beforeend', newHtml);
  
        // Incrémente l’offset pour le prochain clic
        offset += 15;
        loadMoreBtn.setAttribute('data-offset', offset);
  
      } catch (err) {
        console.error('Erreur lors du chargement des figures :', err);
      }
    });
  });
  