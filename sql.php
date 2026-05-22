

<?php
// ====================== DATABASE ======================
$dbFile = 'feedback.sqlite';

function getDB() {
    global $dbFile;
    try {
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Andmebaasi viga: " . $e->getMessage());
    }
}

// Tabeli loomine
function initFeedbackTable() {
    $pdo = getDB();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            timestamp TEXT DEFAULT CURRENT_TIMESTAMP,
            feedback TEXT NOT NULL
        );
    ");
}

// Tagasiside salvestamine (AJAX jaoks)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'save') {
        $text = trim($_POST['feedback'] ?? '');
        
        if (empty($text)) {
            echo json_encode(['success' => false, 'message' => 'Tagasiside ei tohi olla tühi!']);
            exit;
        }

        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("INSERT INTO feedback (feedback) VALUES (?)");
            $stmt->execute([$text]);

            echo json_encode(['success' => true, 'message' => 'Tagasiside salvestatud!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Salvestamine ebaõnnestus.']);
        }
    }
    exit;
}

// Esimene laadimine – loome tabeli
initFeedbackTable();
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagasiside</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 25px; border-radius: 10px; text-align: center; max-width: 400px; }
        .fa { cursor: pointer; font-size: 2.5rem; margin: 10px; transition: all 0.3s; }
    </style>
</head>
<body>

    <!-- Like / Dislike nupud -->
    <i class="fa fa-thumbs-up" style="color: green;" onclick="changeColor(this, 'green')"></i>
    <i class="fa fa-thumbs-down" style="color: gray;" onclick="changeColor(this, 'red')"></i>

    <!-- Dislike Modal -->
    <div id="dislikeModal" class="modal">
        <div class="modal-content">
            <h3>Mis läks valesti?</h3>
            <textarea id="feedbackText" rows="5" style="width:100%;"></textarea><br><br>
            <button id="saadaBtn">Saada</button>
            <button id="closeDislike">Sulge</button>
        </div>
    </div>

    <!-- Thank You Modal -->
    <div id="thankModal" class="modal">
        <div class="modal-content">
            <h3>Aitäh tagasiside eest!</h3>
            <button id="closeThankBtn">Sulge</button>
        </div>
    </div>

    <script>
        // ==================== Muutujad ====================
        let currentColor = "";

        // ==================== Värvi muutmine ====================
        function changeColor(element, color) {
            document.querySelectorAll('.fa').forEach(btn => {
                btn.style.color = "";
                btn.style.transform = "scale(1)";
            });

            element.style.color = color;
            element.style.transform = "scale(1.3)";

            if (color === "red") {
                document.getElementById("dislikeModal").style.display = "flex";
            }
        }

        // ==================== Salvestamine ====================
        document.getElementById("saadaBtn").addEventListener("click", async () => {
            const text = document.getElementById("feedbackText").value.trim();

            if (!text) {
                alert("Palun kirjuta midagi!");
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'save');
                formData.append('feedback', text);

                const response = await fetch('feedback.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById("dislikeModal").style.display = "none";
                    document.getElementById("thankModal").style.display = "flex";
                    document.getElementById("feedbackText").value = "";
                } else {
                    alert(result.message);
                }
            } catch (e) {
                alert("Viga ühenduses serveriga.");
            }
        });

        // ==================== Modalite sulgemine ====================
        document.getElementById("closeDislike").addEventListener("click", () => {
            document.getElementById("dislikeModal").style.display = "none";
        });

        document.getElementById("closeThankBtn").addEventListener("click", () => {
            document.getElementById("thankModal").style.display = "none";
        });

        // Klikk modali taustale
        window.addEventListener("click", (e) => {
            if (e.target.id === "dislikeModal") document.getElementById("dislikeModal").style.display = "none";
            if (e.target.id === "thankModal") document.getElementById("thankModal").style.display = "none";
        });
    </script>
</body>
</html>