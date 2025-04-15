<?php
// Adatbázis kapcsolat létrehozása
$servername = "localhost";
$username = "scrapster"; // Cseréld le a saját MySQL felhasználónevedre
$password = "asdasd2020";         // Cseréld le a saját MySQL jelszavadra
$dbname = "scrapster";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Adatbázis kapcsolati hiba: " . $conn->connect_error);
}

// Karakter kódolás beállítása
$conn->set_charset("utf8mb4");

// Autó törlése, ha a delete_id paramétert megkapjuk
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $auto_id = intval($_GET['delete_id']);
    
    // Törlés az adatbázisból
    $delete_sql = "DELETE FROM autok WHERE id = $auto_id";
    
    if ($conn->query($delete_sql) === TRUE) {
        // Átirányítás vissza az oldalra sikeres üzenettel
        header("Location: aktivhirdetesek.php?status=delete_success");
        exit();
    } else {
        // Átirányítás vissza az oldalra hiba üzenettel
        header("Location: aktivhirdetesek.php?status=delete_error");
        exit();
    }
}

// Összes autó lekérdezése
$sql = "SELECT * FROM autok ORDER BY feltoltes_ideje DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/aktivhirdetesek.css">
    <script src="script/aktivhirdetesek.js"></script>
    <title>Aktív Hirdetések - Scrapster</title>
   
        
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
    
    <div class="container content">
        <h1 class="page-title">Aktív Hirdetések Kezelése</h1>
        
        <div id="statusMessage" class="status-message"></div>
        
        <table>
            <thead>
                <tr>
                    <th>Kép</th>
                    <th>Cím</th>
                    <th>Márka</th>
                    <th>Évjárat</th>
                    <th>Kilowatt</th>
                    <th>Motor állapota</th>
                    <th>Kilométer</th>
                    <th>Szín</th>
                    <th>Feltöltve</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='feltoltott_kepek/" . $row["kep_neve"] . "' alt='" . $row["marka"] . "' class='auto-img'></td>";
                        echo "<td>" . $row["cim"] . "</td>";
                        echo "<td>" . $row["marka"] . "</td>";
                        echo "<td>" . $row["evjarat"] . "</td>";
                        echo "<td>" . $row["kilowatt"] . " kW</td>";
                        echo "<td>" . $row["motorallapota"] . "</td>";
                        echo "<td>" . $row["kilometer"] . " km</td>";
                        echo "<td>" . $row["szin"] . "</td>";
                        echo "<td>" . date('Y.m.d H:i', strtotime($row["feltoltes_ideje"])) . "</td>";
                        echo "<td class='action-buttons'>
                                <a href='modositas.php?id=" . $row["id"] . "' class='btn btn-edit'>Módosítás</a>
                                <a href='aktivhirdetesek.php?delete_id=" . $row["id"] . "' class='btn btn-delete'>Törlés</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' style='text-align: center;'>Nincs aktív hirdetés.</td></tr>";
                }
                ?>
            </tbody>
        </table>
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

<?php
// Kapcsolat bezárása
$conn->close();
?>