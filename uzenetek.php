<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/uzenetek.css">
    <script src="script/uzenetek.js"></script>

    <title>Scrapster - Üzenetek</title>
</head>
<body>
    <header>
        <div class="container nav-container">
            <a href="index.html" class="logo">
                <img src="kepek/logo.png" alt="Scrapster LOGO">
            </a>
            <nav>
                <ul>
                    <li><a href="index.html">Főoldal</a></li>
                    <li><a href="autoink.php">Autóink</a></li>
                    <li><a href="rolunk.html">Rólunk</a></li>
                    <li><a href="kapcsolat.html">Kapcsolat</a></li>
                    <li><a href="igazolas.html">Bontási igazolás</a></li>
                    <li><a href="loginpanel.php">Belépés</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container messages-container">
        <h1 class="messages-title">Beérkezett Üzenetek</h1>
        
        <div id="statusMessage" class="status-message"></div>
        
        <?php
        // Adatbázis kapcsolat létrehozása
        $servername = "localhost";
        $username = "scrapster"; // Cseréld le a saját MySQL felhasználónevedre
        $password = "asdasd2020";         // Cseréld le a saját MySQL jelszavadra
        $dbname = "scrapster";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Kapcsolat ellenőrzése
        if ($conn->connect_error) {
            die("Kapcsolódási hiba: " . $conn->connect_error);
        }
        
        // Üzenet törlése, ha a delete_id paraméter be van állítva
        if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
            $delete_id = $_GET['delete_id'];
            
            $sql_delete = "DELETE FROM kapcsolatok WHERE id = $delete_id";
            
            if ($conn->query($sql_delete) === TRUE) {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const statusMessage = document.getElementById("statusMessage");
                        statusMessage.textContent = "Üzenet sikeresen törölve!";
                        statusMessage.style.backgroundColor = "#4CAF50";
                        statusMessage.style.color = "white";
                        statusMessage.style.display = "block";
                        
                        // URL paraméter eltávolítása
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                </script>';
            } else {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const statusMessage = document.getElementById("statusMessage");
                        statusMessage.textContent = "Hiba történt a törlés során: ' . $conn->error . '";
                        statusMessage.style.backgroundColor = "#f44336";
                        statusMessage.style.color = "white";
                        statusMessage.style.display = "block";
                        
                        // URL paraméter eltávolítása
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                </script>';
            }
        }
        
        // Üzenetek lekérdezése
        $sql = "SELECT * FROM kapcsolatok ORDER BY letrehozva DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            // Üzenetek megjelenítése
            while($row = $result->fetch_assoc()) {
                $id = $row["id"];
                $nev = htmlspecialchars($row["nev"]);
                $email = htmlspecialchars($row["email"]);
                $telefon = htmlspecialchars($row["telefon"]);
                $uzenet = nl2br(htmlspecialchars($row["uzenet"]));
                $letrehozva = date("Y-m-d H:i", strtotime($row["letrehozva"]));
                
                echo '
                <div class="message-card" id="message-'.$id.'">
                    <div class="message-header">
                        <div class="message-user-info">
                            <h3>'.$nev.'</h3>
                            <p>Email: '.$email.' | Telefon: '.$telefon.'</p>
                        </div>
                        <button class="delete-btn" onclick="confirmDelete('.$id.')">Törlés</button>
                    </div>
                    <div class="message-content">
                        '.$uzenet.'
                    </div>
                    <div class="message-date">
                        Küldve: '.$letrehozva.'
                    </div>
                </div>';
            }
        } else {
            echo '<div class="no-messages">Nincsenek beérkezett üzenetek.</div>';
        }
        
        $conn->close();
        ?>
    </div>

    <!-- Megerősítő ablak a törléshez -->
    <div id="confirmDialog" class="confirm-dialog">
        <div class="confirm-dialog-content">
            <h3>Biztosan törölni szeretné ezt az üzenetet?</h3>
            <p>Ez a művelet nem vonható vissza.</p>
            <div class="confirm-dialog-buttons">
                <button id="confirmYes" class="confirm-btn confirm-yes">Igen, törlés</button>
                <button id="confirmNo" class="confirm-btn confirm-no">Mégsem</button>
            </div>
        </div>
    </div>
    

    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-section">
                    <h3>Scrapster</h3>
                    <p>Széles választékban elérhető roncsautók, <br> 
                        megbízható és gyors szolgáltatás,<br>
                         valamint évtizedes szakértelem egy helyen,<br>
                          hogy ügyfeleink a lehető legjobb megoldást<br>
                           találják járműveik bontására<br> és  alkatrészek beszerzésére.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Oldalaink</h3>
                    <p><a href="index.html">Főoldal</a></p>
                    <p><a href="autoink.php">Autóink</a></p>
                    <p><a href="rolunk.html">Rólunk</a></p>
                    <p><a href="kapcsolat.html">Kapcsolat</a></p>
                    <p><a href="igazolas.html">Bontási igazolás</a></p>
                </div>
                
                <div class="footer-section">
                    <h3>Kapcsolat</h3>
                    <p>4700 Mátészalka, Ipari út 8.</p>
                    <p>+36 30 123 4567</p>
                    <p>support@scrapster.com</p>
                    <p>Hétfő - Péntek: 9:00 - 18:00</p>
                    <p>Szombat: 9:00 - 14:00</p>
                    <p>Vasárnap: Zárva</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Scrapster Autóbontó. Minden jog fenntartva.</p>
            </div>
        </div>
    </footer>
</body>
</html>