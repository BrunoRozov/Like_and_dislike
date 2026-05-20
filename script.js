const dislikeModal = document.getElementById("myModal");
const thankModal   = document.getElementById("thankModal");


const saadaBtn           = document.getElementById("saadaBtn");
const closeDislike       = document.getElementById("closeDislike");
const closeThank         = document.getElementById("closeThank");
const closeThankModalBtn = document.getElementById("closeThankBtn");


let buttons = [];

function initButtons() {
    buttons = document.querySelectorAll(".fa");
}


function changeColor(x, color) {
    buttons.forEach(btn => {
        btn.style.color = "";
        btn.style.transform = "scale(1)";
        btn.dataset.active = "false";
    });

    x.style.color = color;
    x.style.transform = "scale(1.3)";
    x.dataset.active = "true";

    if (color === "red" && dislikeModal) {
        dislikeModal.style.display = "flex";
    }
}

if (saadaBtn) {
    saadaBtn.addEventListener("click", () => {
        if (dislikeModal) dislikeModal.style.display = "none";
        if (thankModal) thankModal.style.display = "flex";
    });
}


if (closeDislike) {
    closeDislike.addEventListener("click", () => {
        dislikeModal.style.display = "none";
    });
}

if (closeThank) {
    closeThank.addEventListener("click", () => {
        thankModal.style.display = "none";
    });
}

if (closeThankModalBtn) {
    closeThankModalBtn.addEventListener("click", () => {
        thankModal.style.display = "none";
    });
}

window.addEventListener("click", (e) => {
    if (e.target === dislikeModal) dislikeModal.style.display = "none";
    if (e.target === thankModal) thankModal.style.display = "none";
});

document.addEventListener("DOMContentLoaded", () => {
    initButtons();
    
    console.log({
        dislikeModal: !!dislikeModal,
        thankModal: !!thankModal,
        saadaBtn: !!saadaBtn,
        closeDislike: !!closeDislike,
        closeThank: !!closeThank
    });
});