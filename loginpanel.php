<?php
session_start(); // Start session at the very beginning

// Handle logout - must be before any output
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $helyes_email = "1@1.com";
    $helyes_jelszo = "1";

    $email = $_POST['email'];
    $jelszo = $_POST['password'];

    if ($email == $helyes_email && $jelszo == $helyes_jelszo) {
        $_SESSION['logged_in'] = true;
        // Redirect after setting session to avoid form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $error_message = "Hibás email cím vagy jelszó!";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/loginpanel.css">
    <title>Scrapster - Belépés</title>
    
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
                </ul>
            </nav>
        </div>
    </header>
    
    <section class="login-section">
        <div class="login-container">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <!-- Display buttons after successful login -->
                <div class="login-header">
                    <h2>Üdvözöljük!</h2>
                    <p>Válasszon az alábbi lehetőségek közül</p>
                </div>
                
                <div class="button-grid">
                    <a href="autofeltoltes.php" class="btn">Autófeltöltés</a>
                    <a href="aktivhirdetesek.php" class="btn">Aktív autók</a>
                    <a href="uzenetek.php" class="btn">Kapcsolat</a>
                </div>
                
                <div class="form-group" style="margin-top: 1.5rem;">
                    <a href="?logout=1" class="btn" style="width: 100%; background-color: #7f8c8d;">Kijelentkezés</a>
                </div>
                
            <?php else: ?>
                <!-- Display login form if not logged in -->
                <div class="login-header">
                    <h2>Bejelentkezés</h2>
                    <p>Lépjen be fiókjába a feltöltéshez</p>
                </div>
                
                <?php if (isset($error_message)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email cím</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Jelszó</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <button type="submit" class="btn" style="width: 100%;">Bejelentkezés</button>
                    </div>
                </form>
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
</body>
</html>