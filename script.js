document.addEventListener('DOMContentLoaded', function() {
    // URL paraméterek kinyerése
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    
    // Státusz üzenet elem
    const statusMessage = document.getElementById('statusMessage');
    
    // Különböző státuszok kezelése
    if (status === 'success') {
        statusMessage.textContent = 'Sikeres üzenetküldés!';
        statusMessage.style.backgroundColor = '#4CAF50';
        statusMessage.style.color = 'white';
        statusMessage.style.display = 'block';
    } else if (status === 'error') {
        statusMessage.textContent = 'Hiba történt az üzenet küldése során. Kérjük próbálja újra.';
        statusMessage.style.backgroundColor = '#f44336';
        statusMessage.style.color = 'white';
        statusMessage.style.display = 'block';
    }
    
    // URL-ből a paraméterek eltávolítása az állapot ellenőrzése után
    if (status) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});