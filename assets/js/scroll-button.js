document.addEventListener("DOMContentLoaded", function () {
    const scrollButton = document.getElementById("scrollButton");

    if (scrollButton) {
        window.addEventListener("scroll", function () {
            if (window.scrollY > 200) {
                scrollButton.style.display = "block";
            } else {
                scrollButton.style.display = "none";
            }
        });

        scrollButton.addEventListener("click", function () {
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            const scrollTop = window.scrollY;

            if (scrollTop + clientHeight < scrollHeight - 100) {
                window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });
                scrollButton.innerHTML = "⬆️";
            } else {
                window.scrollTo({ top: 0, behavior: "smooth" });
                scrollButton.innerHTML = "⬇️";
            }
        });
    }
});
