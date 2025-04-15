<?php
$servername = "localhost";
$username = "scrapster"; // Cseréld le a saját MySQL felhasználónevedre
$password = "asdasd2020";         // Cseréld le a saját MySQL jelszavadra
$dbname = "scrapster";

// Kapcsolódás az adatbázishoz
$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolódás ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Adatok beolvasása az űrlapról
$nev = $_POST['name'];
$email = $_POST['email'];
$telefon = $_POST['phone'];
$uzenet = $_POST['message'];
$oldal = isset($_POST['oldal']) ? $_POST['oldal'] : 'kapcsolat.html';

// SQL lekérdezés az adatok beszúrásához
$sql = "INSERT INTO kapcsolatok (nev, email, telefon, uzenet) VALUES ('$nev', '$email', '$telefon', '$uzenet')";

if ($conn->query($sql) === TRUE) {
    // Sikeres beküldés, átirányítás az eredeti oldalra sikeres állapottal
    header("Location: $oldal?status=success");
} else {
    // Hiba esetén átirányítás az eredeti oldalra hibaüzenettel
    header("Location: $oldal?status=error");
}

$conn->close();
?>