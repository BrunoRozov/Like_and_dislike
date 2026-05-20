// ==================== SQLite ====================
let db;

async function initSQLite() {
    try {
        const SQL = await initSqlJs({
            locateFile: file => `https://cdnjs.cloudflare.com/ajax/libs/sql.js/1.11.0/${file}`
        });

        db = new SQL.Database();

        db.run(`
            CREATE TABLE IF NOT EXISTS feedback (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                timestamp TEXT DEFAULT CURRENT_TIMESTAMP,
                feedback TEXT
            );
        `);

        console.log("✅ SQLite andmebaas valmis!");
    } catch (err) {
        console.error("SQLite viga:", err);
        alert("Andmebaasi laadimine ebaõnnestus. Kontrolli internetiühendust.");
    }
}

// Salvesta tagasiside
function saveFeedback(text) {
    if (!text || text.trim() === "") {
        alert("Palun kirjuta midagi enne saatmist!");
        return false;
    }

    try {
        const timestamp = new Date().toISOString();
        db.run("INSERT INTO feedback (timestamp, feedback) VALUES (?, ?)", [timestamp, text]);
        console.log("💾 Salvestatud:", text);
        return true;
    } catch (e) {
        console.error(e);
        alert("Salvestamine ebaõnnestus.");
        return false;
    }
}

// ==================== Modal loogika ====================
const error = document.getElementById("myModal");
const thankModal = document.getElementById("thankModal");
const saadaBtn = document.getElementById("saadaBtn");
const feedbackTextarea = document.getElementById("feedbackText");

const closeDislike = document.getElementById("closeDislike");
const closeThank = document.getElementById("closeThank");
const closeThankBtn = document.getElementById("closeThankBtn");

console.log("✅ Modal!");

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

    if (color === "red") dislikeModal.style.display = "flex";
}

// Saada vajutus
saadaBtn?.addEventListener("click", () => {
    const text = feedbackTextarea.value.trim();
    
    if (saveFeedback(text)) {
        dislikeModal.style.display = "none";
        thankModal.style.display = "flex";
        feedbackTextarea.value = ""; // tühjenda väli
    }
});

// Sulgemised
closeDislike?.addEventListener("click", () => dislikeModal.style.display = "none");
closeThank?.addEventListener("click", () => thankModal.style.display = "none");
closeThankBtn?.addEventListener("click", () => thankModal.style.display = "none");

window.addEventListener("click", (e) => {
    if (e.target === dislikeModal) dislikeModal.style.display = "none";
    if (e.target === thankModal) thankModal.style.display = "none";
});

// Käivitamine
document.addEventListener("DOMContentLoaded", async () => {
    initButtons();
    await initSQLite();
});