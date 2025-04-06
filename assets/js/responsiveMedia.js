document.addEventListener('DOMContentLoaded', function () {
    const mediaCollapse = document.getElementById('mediaCollapse');

    function handleResize() {
        if (!mediaCollapse) return;

        if (window.innerWidth >= 768) {
            mediaCollapse.classList.add('show');
        } else {
            mediaCollapse.classList.remove('show');
        }
    }

    handleResize();
    window.addEventListener('resize', handleResize);
});
