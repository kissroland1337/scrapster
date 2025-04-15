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

// Ellenőrizzük, hogy kaptunk-e ID-t
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: aktivhirdetesek.php");
    exit();
}

$auto_id = intval($_GET['id']);

// Ha a form elküldésre került
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adatok tisztítása
    $cim = $conn->real_escape_string($_POST['cim']);
    $marka = $conn->real_escape_string($_POST['marka']);
    $evjarat = $conn->real_escape_string($_POST['evjarat']);
    $kilowatt = $conn->real_escape_string($_POST['kilowatt']);
    $motorallapota = $conn->real_escape_string($_POST['motorallapota']);
    $kilometer = $conn->real_escape_string($_POST['kilometer']);
    $szin = $conn->real_escape_string($_POST['szin']);
    
    // Frissítés az adatbázisban
    $update_sql = "UPDATE autok SET 
                    cim = '$cim', 
                    marka = '$marka', 
                    evjarat = '$evjarat', 
                    kilowatt = '$kilowatt', 
                    motorallapota = '$motorallapota', 
                    kilometer = '$kilometer', 
                    szin = '$szin' 
                    WHERE id = $auto_id";
    
    if ($conn->query($update_sql) === TRUE) {
        header("Location: aktivhirdetesek.php?status=update_success");
        exit();
    } else {
        header("Location: aktivhirdetesek.php?status=update_error");
        exit();
    }
}

// Autó adatainak lekérdezése
$sql = "SELECT * FROM autok WHERE id = $auto_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: aktivhirdetesek.php");
    exit();
}

$auto = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/modositas.css">

    <title>Hirdetés módosítása - Scrapster</title>
   
    
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
        <h1 class="page-title">Hirdetés módosítása</h1>
        
        <div class="form-container">
            <img src="feltoltott_kepek/<?php echo $auto['kep_neve']; ?>" alt="<?php echo $auto['marka']; ?>" class="auto-img">
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="cim">Cím:</label>
                    <input type="text" id="cim" name="cim" value="<?php echo $auto['cim']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="marka">Márka:</label>
                    <input type="text" id="marka" name="marka" value="<?php echo $auto['marka']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="evjarat">Évjárat:</label>
                    <input type="text" id="evjarat" name="evjarat" value="<?php echo $auto['evjarat']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="kilowatt">Kilowatt:</label>
                    <input type="text" id="kilowatt" name="kilowatt" value="<?php echo $auto['kilowatt']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="motorallapota">Motor állapota:</label>
                    <select id="motorallapota" name="motorallapota" required>
                        <option value="Hibátlan" <?php echo ($auto['motorallapota'] == 'Hibátlan') ? 'selected' : ''; ?>>Hibátlan</option>
                        <option value="Jó" <?php echo ($auto['motorallapota'] == 'Jó') ? 'selected' : ''; ?>>Jó</option>
                        <option value="Közepes" <?php echo ($auto['motorallapota'] == 'Közepes') ? 'selected' : ''; ?>>Közepes</option>
                        <option value="Rossz" <?php echo ($auto['motorallapota'] == 'Rossz') ? 'selected' : ''; ?>>Rossz</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="kilometer">Kilométeróra állása:</label>
                    <input type="text" id="kilometer" name="kilometer" value="<?php echo $auto['kilometer']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="szin">Szín:</label>
                    <input type="text" id="szin" name="szin" value="<?php echo $auto['szin']; ?>" required>
                </div>
                
                <div class="buttons">
                    <a href="aktivhirdetesek.php" class="btn btn-cancel">Mégsem</a>
                    <button type="submit" class="btn btn-primary">Mentés</button>
                </div>
            </form>
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
                           találják járműveik bontására<br> és alkatrészek beszerzésére.</p>
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