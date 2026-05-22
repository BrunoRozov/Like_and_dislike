

<?php
// ====================== ANDMEBAAS ======================
$dbFile = 'feedback.sqlite';

function getDB() {
    global $dbFile;
    try {
        $pdo = new PDO('sqlite:' . $dbFile, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return $pdo;
    } catch (PDOException $e) {
        die("Andmebaasi viga: " . $e->getMessage());
    }
}

// Tabeli loomine
$pdo = getDB();
$pdo->exec("
    CREATE TABLE IF NOT EXISTS feedback (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        timestamp TEXT DEFAULT CURRENT_TIMESTAMP,
        feedback TEXT NOT NULL
    );
");

// AJAX salvestamine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_feedback') {
    header('Content-Type: application/json');
    $text = trim($_POST['feedback'] ?? '');

    if (empty($text)) {
        echo json_encode(['success' => false, 'message' => 'Tagasiside ei tohi olla tühi!']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO feedback (feedback) VALUES (?)");
        $stmt->execute([$text]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Salvestamine ebaõnnestus']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Like and dislike</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

    <header>
        <section>
            <div class="content">
                <div class="content1">
                    <p>Wow see video oli nii lahe</p>

                    <div class="like">
                        <i onclick="changeColor(this, 'green')" class="fa fa-thumbs-up"></i>
                        <i onclick="changeColor(this, 'red')" class="fa fa-thumbs-down"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dislike Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeDislike">&times;</span>
                <h2>Dislike</h2>
                <p>Miks sulle video ei meeldinud?</p>
                
                <div class="textarea">
                    <textarea id="feedbackText" placeholder="Kirjuta siia oma tagasiside..."></textarea>
                </div>
                
                <div class="saada">
                    <button id="saadaBtn">Saada</button>
                </div>
            </div>
        </div>

        <!-- Thank You Modal -->
        <div id="thankModal" class="modal">
            <div class="modal-content thank-content">
                <span class="close" id="closeThank">&times;</span>
                <h2>Aitäh!</h2>
                <p>Sinu tagasiside on vastu võetud.</p>
                
                <div class="saada">
                    <button id="closeThankBtn">Sulge</button>
                </div>
            </div>
        </div>
    </header>

    <script>
        const dislikeModal1 = document.getElementById("myModal");
        const thankModal1   = document.getElementById("thankModal");

        const saadaBtn1           = document.getElementById("saadaBtn");
        const closeDislike1       = document.getElementById("closeDislike");
        const closeThank1         = document.getElementById("closeThank");
        const closeThankModalBtn  = document.getElementById("closeThankBtn");

        console.log("✅ Essa!");

        let buttons1 = [];

        function initButtons() {
            buttons1 = document.querySelectorAll(".fa");
        }

        function changeColor(x, color) {
            buttons1.forEach(btn => {
                btn.style.color = "";
                btn.style.transform = "scale(1)";
                btn.dataset.active = "false";
            });

            x.style.color = color;
            x.style.transform = "scale(1.3)";
            x.dataset.active = "true";

            if (color === "red" && dislikeModal1) {
                dislikeModal1.style.display = "flex";
            }
        }

        // ==================== Salvestamine (PHP + AJAX) ====================
        if (saadaBtn1) {
            saadaBtn1.addEventListener("click", async () => {
                const text = document.getElementById("feedbackText").value.trim();

                if (!text) {
                    alert("Palun kirjuta tagasiside!");
                    return;
                }

                try {
                    const formData = new FormData();
                    formData.append('action', 'save_feedback');
                    formData.append('feedback', text);

                    const response = await fetch('feedback.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        if (dislikeModal1) dislikeModal1.style.display = "none";
                        if (thankModal1) thankModal1.style.display = "flex";
                        document.getElementById("feedbackText").value = "";
                    } else {
                        alert(result.message || "Salvestamine ebaõnnestus");
                    }
                } catch (e) {
                    alert("Viga serveriga ühendamisel.");
                }
            });
        }

        if (closeDislike1) {
            closeDislike1.addEventListener("click", () => {
                if (dislikeModal1) dislikeModal1.style.display = "none";
            });
        }

        if (closeThank1) {
            closeThank1.addEventListener("click", () => {
                if (thankModal1) thankModal1.style.display = "none";
            });
        }

        if (closeThankModalBtn) {
            closeThankModalBtn.addEventListener("click", () => {
                if (thankModal1) thankModal1.style.display = "none";
            });
        }

        window.addEventListener("click", (e) => {
            if (e.target === dislikeModal1) dislikeModal1.style.display = "none";
            if (e.target === thankModal1) thankModal1.style.display = "none";
        });

        document.addEventListener("DOMContentLoaded", () => {
            initButtons();
            
            console.log({
                dislikeModal: !!dislikeModal1,
                thankModal: !!thankModal1,
                saadaBtn: !!saadaBtn1,
                closeDislike: !!closeDislike1,
                closeThank: !!closeThank1,
            });

            console.log("✅ Script loaded!");
        });
    </script>
</body>
</html>