document.addEventListener('DOMContentLoaded', function () {
    const mediaCollapse = document.getElementById('mediaCollapse');
  
    function handleResize() {
      if (window.innerWidth >= 768) {
        // Desktop : toujours affiché
        mediaCollapse.classList.add('show');
      } else {
        // Mobile : toujours caché par défaut
        mediaCollapse.classList.remove('show');
      }
    }
  
    // Appel initial
    handleResize();
  
    // Sur changement de taille d'écran
    window.addEventListener('resize', handleResize);
  });
  