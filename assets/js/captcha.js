document.addEventListener("DOMContentLoaded", function () {
    const captchaButton = document.querySelector(".btn-captcha-renew");

    if (captchaButton) {
        captchaButton.addEventListener("click", function () {
            const captchaImg = document.querySelector(".captcha-container img");
            if (captchaImg) {
                let src = captchaImg.src;
                captchaImg.src = src.split("?")[0] + "?" + new Date().getTime(); // Force le rechargement de l'image
            }
        });
    }
});
