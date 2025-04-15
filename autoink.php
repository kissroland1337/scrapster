<?php
// Kapcsolódás az adatbázishoz
require_once 'config.php';

// Márka szűrő kezelése
$selected_marka = isset($_GET['marka']) ? $_GET['marka'] : '';

// Összes elérhető márka lekérdezése
$markak_sql = "SELECT DISTINCT marka FROM autok ORDER BY marka";
$markak_result = $conn->query($markak_sql);
$markak = [];
if ($markak_result && $markak_result->num_rows > 0) {
    while($row = $markak_result->fetch_assoc()) {
        $markak[] = $row['marka'];
    }
}

// Autók lekérdezése az adatbázisból, márka szerinti szűréssel
$sql = "SELECT autok.*, GROUP_CONCAT(auto_kepek.kep_neve) as osszes_kep 
        FROM autok 
        LEFT JOIN auto_kepek ON autok.id = auto_kepek.auto_id";

// Ha van kiválasztott márka, hozzáadjuk a szűrőt
if (!empty($selected_marka)) {
    $sql .= " WHERE autok.marka = '" . $conn->real_escape_string($selected_marka) . "'";
}

$sql .= " GROUP BY autok.id ORDER BY feltoltes_ideje DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/autoink.css">
    <script src="script/modal.js"></script>
    <title>Scrapster - Autóink</title>
    
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
    
    <section class="cars-section" id="cars">
        <div class="container">
            <div class="section-title">
                <h2>Elérhető roncsautók</h2>
                <p>Az alábbi roncsautókat kínáljuk eladásra vagy alkatrészként</p>
            </div>
            
            <!-- Márka szűrő -->
            <div class="filter-container">
                <form action="" method="get" class="filter-form">
                    <label for="marka-filter">Márka szerinti szűrés:</label>
                    <select name="marka" id="marka-filter" onchange="this.form.submit()">
                        <option value="">Összes márka</option>
                        <?php foreach($markak as $marka): ?>
                            <option value="<?php echo htmlspecialchars($marka); ?>" <?php echo ($selected_marka === $marka) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($marka); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <?php if(!empty($selected_marka)): ?>
            <div class="active-filters">
                <div class="filter-tag">
                    Márka: <?php echo htmlspecialchars($selected_marka); ?>
                    <a href="autoink.php">&times;</a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if($result && $result->num_rows > 0): ?>
            <div class="cars-container">
                <?php while($row = $result->fetch_assoc()): 
                    // Alap kép ha nincs más megadva
                    $foKep = !empty($row['kep_neve']) ? $row['kep_neve'] : 'nincs_kep.jpg';
                    
                    // Összes kép kezelése
                    $kepek = [];
                    if(!empty($row['osszes_kep'])) {
                        $kepek = explode(',', $row['osszes_kep']);
                    }
                    // Ha nincs más kép, akkor csak a fő képet használjuk
                    if(empty($kepek)) {
                        $kepek[] = $foKep;
                    }
                    // Egyedi azonosító minden autóhoz a JavaScript számára
                    $autoId = 'auto_' . $row['id'];
                ?>
                <div class="car-card">
                    <div class="car-image">
                        <?php foreach($kepek as $index => $kep): ?>
                            <img 
                                src="kepek/autok/<?php echo htmlspecialchars($kep); ?>" 
                                alt="<?php echo htmlspecialchars($row['cim']); ?>" 
                                class="car-img <?php echo $index === 0 ? 'active' : ''; ?>"
                                id="<?php echo $autoId . '_img_' . $index; ?>"
                                style="<?php echo $index === 0 ? '' : 'display: none;'; ?>"
                                onclick="openModal('<?php echo htmlspecialchars($kep); ?>', '<?php echo $autoId; ?>', <?php echo $index; ?>)"
                            >
                        <?php endforeach; ?>
                        
                        <?php if(count($kepek) > 1): ?>
                        <!-- Képváltó nyilak -->
                        <div class="image-arrows">
                            <button class="arrow-btn prev-btn" onclick="changeImage('<?php echo $autoId; ?>', 'prev'); event.stopPropagation();">&#10094;</button>
                            <button class="arrow-btn next-btn" onclick="changeImage('<?php echo $autoId; ?>', 'next'); event.stopPropagation();">&#10095;</button>
                        </div>
                        
                        <!-- Pontok a képekhez -->
                        <div class="image-nav">
                            <?php foreach($kepek as $index => $kep): ?>
                                <div 
                                    class="image-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    onclick="showImage('<?php echo $autoId; ?>', <?php echo $index; ?>); event.stopPropagation();"
                                    id="<?php echo $autoId . '_dot_' . $index; ?>"
                                ></div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="car-details">
                        <h3 class="car-title"><?php echo htmlspecialchars($row['cim']); ?></h3>
                        <div class="car-info">
                            <div>
                                <span>Márka:</span>
                                <span><?php echo htmlspecialchars($row['marka']); ?></span>
                            </div>
                            <div>
                                <span>Évjárat:</span>
                                <span><?php echo htmlspecialchars($row['evjarat']); ?></span>
                            </div>
                            <div>
                                <span>Kilowatt:</span>
                                <span><?php echo htmlspecialchars($row['kilowatt']); ?></span>
                            </div>
                            <div>
                                <span>Motor állapota:</span>
                                <span><?php echo htmlspecialchars($row['motorallapota']); ?></span>
                            </div>
                            <div>
                                <span>Futott km:</span>
                                <span><?php echo htmlspecialchars($row['kilometer']); ?></span>
                            </div>
                            <div>
                                <span>Szín:</span>
                                <span><?php echo htmlspecialchars($row['szin']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="no-cars">
                <h3>Jelenleg nincs elérhető autó<?php echo !empty($selected_marka) ? ' a kiválasztott márkából' : ''; ?></h3>
                <p>Kérjük nézzen vissza később, vagy adjon hozzá új autót.</p>
                <a href="autofeltoltes.php" class="btn">Autó feltöltése</a>
                <?php if(!empty($selected_marka)): ?>
                <a href="autoink.php" class="btn" style="background-color: var(--secondary);">Összes autó megtekintése</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
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

    <!-- Modális ablak a nagyított képhez és navigációhoz -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <button class="modal-prev-btn" onclick="changeModalImage('prev')">&laquo;</button>
        <img class="modal-content" id="modalImage">
        <button class="modal-next-btn" onclick="changeModalImage('next')">&raquo;</button>
        <div class="modal-dots" id="modalDots"></div>
    </div>

   
</body>
</html>