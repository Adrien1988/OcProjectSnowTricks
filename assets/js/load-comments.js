// assets/js/load-comments.js
document.addEventListener('DOMContentLoaded', () => {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const commentsContainer = document.getElementById('comments-container');

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            fetch(loadMoreBtn.dataset.url)
                .then((response) => response.json())
                .then((data) => {
                    if (data.comments) {
                        data.comments.forEach((comment) => {
                            const div = document.createElement('div');
                            div.classList.add('comment', 'mb-3', 'p-3', 'border', 'rounded');
                            div.innerHTML = `
                                <h6 class="fw-bold">${comment.author}</h6>
                                <p class="text-muted">${comment.createdAt}</p>
                                <p>${comment.content}</p>
                            `;
                            commentsContainer.appendChild(div);
                        });

                        // Supprime le bouton une fois les commentaires chargés
                        loadMoreBtn.remove();
                    }
                });
        });
    }
});
