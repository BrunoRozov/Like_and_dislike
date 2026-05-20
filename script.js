const modal = document.getElementById("myModal");
const closeBtn = document.querySelector(".close");

// Cache all buttons once
let buttons = [];

// Initialize buttons
function initButtons() {
    buttons = document.querySelectorAll(".fa");
}

function changeColor(x, color) {
    // Reset all buttons
    buttons.forEach(btn => {
        btn.style.color = "";
        btn.style.transform = "scale(1)";
        btn.dataset.active = "false";
    });

    // Activate the clicked one
    x.style.color = color;
    x.style.transform = "scale(1.3)";
    x.dataset.active = "true";

    if (color === "red") {
        modal.style.display = "flex";
    }
}

// Close modal handlers
closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
});

window.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});

// Better: Run initialization when DOM is ready
document.addEventListener("DOMContentLoaded", initButtons);