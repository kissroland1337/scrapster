// Törlés megerősítő funkció
let deleteId = null;

function confirmDelete(id) {
    deleteId = id;
    const confirmDialog = document.getElementById('confirmDialog');
    confirmDialog.style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
    const confirmDialog = document.getElementById('confirmDialog');
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');
    
    confirmYes.addEventListener('click', function() {
        if (deleteId) {
            window.location.href = 'uzenetek.php?delete_id=' + deleteId;
        }
        confirmDialog.style.display = 'none';
    });
    
    confirmNo.addEventListener('click', function() {
        confirmDialog.style.display = 'none';
        deleteId = null;
    });
});