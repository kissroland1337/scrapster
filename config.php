<?php
// config.php - Adatbázis kapcsolat beállítások
$servername = "localhost";
$username = "scrapster"; // Cseréld le a saját MySQL felhasználónevedre
$password = "asdasd2020";         // Cseréld le a saját MySQL jelszavadra
$dbname = "scrapster";

// Kapcsolat létrehozása
$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Adatbázis kapcsolat hiba: " . $conn->connect_error);
}

// Magyar karakterkészlet beállítása
$conn->set_charset("utf8mb4");
?>