<?php
// Kapcsolódás az adatbázishoz
require_once 'config.php';

// Változók inicializálása
$sikeres_feltoltes = false;
$hibauzenet = "";

// Űrlap feldolgozása
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adatok tisztítása és mentése
    $cim = $conn->real_escape_string($_POST['cim']);
    $evjarat = $conn->real_escape_string($_POST['evjarat']);
    $kilowatt = $conn->real_escape_string($_POST['kilowatt']);
    $motorallapota = $conn->real_escape_string($_POST['motorallapota']);
    $kilometer = $conn->real_escape_string($_POST['kilometer']);
    $szin = $conn->real_escape_string($_POST['szin']);

    // Ismert autómárkák listája
    $ismert_markak = array(
        'Opel', 'Volkswagen', 'BMW', 'Audi', 'Mercedes', 'Mercedes-Benz', 'Suzuki', 'Toyota', 
        'Honda', 'Mazda', 'Nissan', 'Mitsubishi', 'Hyundai', 'Kia', 'Renault', 'Peugeot', 'Citroen', 
        'Fiat', 'Ford', 'Seat', 'Skoda', 'Chevrolet', 'Daewoo', 'Dacia'
    );

    // Márka kinyerése a címből
    $marka = '';
    $cim_words = explode(' ', $cim);
    foreach ($ismert_markak as $ismert_marka) {
        foreach ($cim_words as $word) {
            if (strtolower($word) === strtolower($ismert_marka)) {
                $marka = $ismert_marka;
                break 2;
            }
        }
    }

    // Ha nem találtunk ismert márkát, akkor az első szó legyen a márka
    if (empty($marka)) {
        $marka = $cim_words[0];
    }

    $marka = $conn->real_escape_string($marka);
    
    // Képek feltöltés kezelése
    if(isset($_FILES['kepek']) && !empty($_FILES['kepek']['name'][0])) {
        // Mappa létezésének ellenőrzése, ha nem létezik létrehozzuk
        if(!is_dir('kepek/autok/')) {
            mkdir('kepek/autok/', 0777, true);
        }
        
        // Először mentsük el az autó adatait - most már a márkával együtt
        $sql = "INSERT INTO autok (cim, marka, evjarat, kilowatt, motorallapota, kilometer, szin) 
                VALUES ('$cim', '$marka', '$evjarat', '$kilowatt', '$motorallapota', '$kilometer', '$szin')";
        
        if ($conn->query($sql) === TRUE) {
            $auto_id = $conn->insert_id; // Az új autó ID-je
            $sikeres_feltoltes = true;
            
            // Képek feldolgozása
            $feltoltott_kepek = 0;
            $osszes_kep = count($_FILES['kepek']['name']);
            
            for($i = 0; $i < $osszes_kep; $i++) {
                if($_FILES['kepek']['error'][$i] == 0) {
                    $engedelyezett_tipusok = array('jpg', 'jpeg', 'png', 'gif');
                    $fajlnev = $_FILES['kepek']['name'][$i];
                    $fajl_kiterjesztes = strtolower(pathinfo($fajlnev, PATHINFO_EXTENSION));
                    
                    // Ellenőrizzük a fájl típusát
                    if(in_array($fajl_kiterjesztes, $engedelyezett_tipusok)) {
                        // Egyedi fájlnév generálása az ütközések elkerülése érdekében
                        $uj_fajlnev = uniqid() . '_' . $i . '.' . $fajl_kiterjesztes;
                        $feltoltes_utvonal = 'kepek/autok/' . $uj_fajlnev;
                        
                        // Fájl feltöltése
                        if(move_uploaded_file($_FILES['kepek']['tmp_name'][$i], $feltoltes_utvonal)) {
                            // Kép mentése az adatbázisba
                            $kep_sql = "INSERT INTO auto_kepek (auto_id, kep_neve) VALUES ('$auto_id', '$uj_fajlnev')";
                            
                            if ($conn->query($kep_sql) === TRUE) {
                                $feltoltott_kepek++;
                                
                                // Az első feltöltött képet állítsuk be fő képként az autó táblában
                                if($feltoltott_kepek == 1) {
                                    $update_sql = "UPDATE autok SET kep_neve = '$uj_fajlnev' WHERE id = $auto_id";
                                    $conn->query($update_sql);
                                }
                            } else {
                                $hibauzenet .= "Hiba történt a kép adatbázisba mentése közben: " . $conn->error . "<br>";
                            }
                        } else {
                            $hibauzenet .= "Hiba történt a(z) " . ($i+1) . ". fájl feltöltése közben.<br>";
                        }
                    } else {
                        $hibauzenet .= "A(z) " . ($i+1) . ". fájl típusa nem engedélyezett. Csak JPG, JPEG, PNG és GIF fájlok engedélyezettek.<br>";
                    }
                } elseif($_FILES['kepek']['error'][$i] != 4) { // 4 = UPLOAD_ERR_NO_FILE, tehát ha nincs feltöltve, ne jelezzen hibát
                    $hibauzenet .= "Hiba történt a(z) " . ($i+1) . ". fájl feltöltése közben (kód: " . $_FILES['kepek']['error'][$i] . ").<br>";
                }
            }
            
            if($feltoltott_kepek == 0) {
                $hibauzenet = "Legalább egy kép feltöltése szükséges.";
                // Töröljük a létrehozott autót, ha nem sikerült képet feltölteni
                $delete_sql = "DELETE FROM autok WHERE id = $auto_id";
                $conn->query($delete_sql);
                $sikeres_feltoltes = false;
            } else {
                $sikeres_feltoltes = true;
            }
        } else {
            $hibauzenet = "Hiba történt az adatok mentése közben: " . $conn->error;
        }
    } else {
        $hibauzenet = "Kérjük, válasszon ki legalább egy képet feltöltésre.";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/autofeltoltes.css">
    <script src="script/script.js"></script>
    <title>Scrapster - Autó feltöltés</title>
  
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
                </ul>
            </nav>
        </div>
    </header>
    
    <section class="login-section">
        <div class="login-container">
            <div class="login-header">
                <h2>Autó feltöltés</h2>
                <p>Töltse ki az alábbi mezőket az autó hozzáadásához</p>
            </div>
            
            <?php if($sikeres_feltoltes): ?>
            <div class="alert alert-success">
                Az autó sikeresen feltöltve! <a href="autoink.php">Megtekintés</a>
            </div>
            <?php elseif($hibauzenet): ?>
            <div class="alert alert-danger">
                <?php echo $hibauzenet; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="cim">Cím</label>
                    <input type="text" id="cim" name="cim" required>
                </div>

                <div class="form-group">
                    <label for="evjarat">Évjárat</label>
                    <input type="text" id="evjarat" name="evjarat" required>
                </div>

                <div class="form-group">
                    <label for="kilowatt">Kilowatt</label>
                    <input type="text" id="kilowatt" name="kilowatt" required>
                </div>

                <div class="form-group">
                    <label for="motorallapota">Motor állapota</label>
                    <input type="text" id="motorallapota" name="motorallapota" required>
                </div>

                <div class="form-group">
                    <label for="kilometer">Futott kilóméter</label>
                    <input type="text" id="kilometer" name="kilometer" required>
                </div>

                <div class="form-group">
                    <label for="szin">Szín</label>
                    <input type="text" id="szin" name="szin" required>
                </div>

                <div class="form-group">
                    <label for="kepek">Képek</label>
                    <input type="file" id="kepek" name="kepek[]" multiple required onchange="showImagePreviews(this)">
                    <div class="kepek-info">Válasszon ki több képet. Az első kép lesz a fő kép.</div>
                    <div id="kep-elokezet-container"></div>
                </div>
                
                <div class="form-group" style="margin-top: 1.5rem;">
                    <button type="submit" class="btn" style="width: 100%;">Feltöltés</button>
                </div>
            </form>
        </div>
    </section>
    
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