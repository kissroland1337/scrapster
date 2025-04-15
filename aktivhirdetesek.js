document.addEventListener('DOMContentLoaded', function() {
    // URL paraméterek kinyerése
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    
    // Státusz üzenet elem
    const statusMessage = document.getElementById('statusMessage');
    
    // Különböző státuszok kezelése
    if (status === 'delete_success') {
        statusMessage.textContent = 'A hirdetés sikeresen törölve!';
        statusMessage.classList.add('success');
        statusMessage.style.display = 'block';
    } else if (status === 'delete_error') {
        statusMessage.textContent = 'Hiba történt a hirdetés törlése során. Kérjük próbálja újra.';
        statusMessage.classList.add('error');
        statusMessage.style.display = 'block';
    } else if (status === 'update_success') {
        statusMessage.textContent = 'A hirdetés sikeresen frissítve!';
        statusMessage.classList.add('success');
        statusMessage.style.display = 'block';
    } else if (status === 'update_error') {
        statusMessage.textContent = 'Hiba történt a hirdetés frissítése során. Kérjük próbálja újra.';
        statusMessage.classList.add('error');
        statusMessage.style.display = 'block';
    }
    
    // Törlés megerősítése
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Biztosan törölni szeretnéd ezt a hirdetést?')) {
                e.preventDefault();
            }
        });
    });
    
    // URL-ből a paraméterek eltávolítása az állapot ellenőrzése után
    if (status) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});