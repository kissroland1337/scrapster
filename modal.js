// Globális változók a modális képváltáshoz
let currentCarId = '';
let currentImageSources = [];
let currentModalIndex = 0;

// Képgaléria funkciók
function showImage(autoId, index) {
    const images = document.querySelectorAll('[id^="' + autoId + '_img_"]');
    const dots = document.querySelectorAll('[id^="' + autoId + '_dot_"]');
    
    for (let i = 0; i < images.length; i++) {
        images[i].style.display = 'none';
        dots[i].classList.remove('active');
    }
    
    images[index].style.display = 'block';
    dots[index].classList.add('active');
}

function changeImage(autoId, direction) {
    const images = document.querySelectorAll('[id^="' + autoId + '_img_"]');
    
    let currentIndex = -1;
    for (let i = 0; i < images.length; i++) {
        if (images[i].style.display !== 'none') {
            currentIndex = i;
            break;
        }
    }
    
    let nextIndex;
    if (direction === 'next') {
        nextIndex = (currentIndex + 1) % images.length;
    } else {
        nextIndex = (currentIndex - 1 + images.length) % images.length;
    }
    
    showImage(autoId, nextIndex);
}

function openModal(imageName, autoId, clickedIndex) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    const modalDots = document.getElementById("modalDots");
    
    currentCarId = autoId;
    currentModalIndex = clickedIndex || 0;
    
    const images = document.querySelectorAll('[id^="' + autoId + '_img_"]');
    currentImageSources = [];
    
    images.forEach(img => {
        const srcParts = img.src.split('/');
        const imgName = srcParts[srcParts.length - 1];
        currentImageSources.push(imgName);
    });
    
    modalDots.innerHTML = '';
    currentImageSources.forEach((src, idx) => {
        const dot = document.createElement('div');
        dot.className = 'modal-dot ' + (idx === currentModalIndex ? 'active' : '');
        dot.onclick = function() { showModalImage(idx); };
        modalDots.appendChild(dot);
    });
    
    modalImg.src = "kepek/autok/" + currentImageSources[currentModalIndex];
    modal.style.display = "block";
    
    const prevBtn = document.querySelector('.modal-prev-btn');
    const nextBtn = document.querySelector('.modal-next-btn');
    
    if (currentImageSources.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
        modalDots.style.display = 'none';
    } else {
        prevBtn.style.display = 'flex';
        nextBtn.style.display = 'flex';
        modalDots.style.display = 'flex';
    }
}

function closeModal() {
    const modal = document.getElementById("imageModal");
    modal.style.display = "none";
}

function showModalImage(index) {
    const modalImg = document.getElementById("modalImage");
    const dots = document.querySelectorAll('.modal-dot');
    
    currentModalIndex = index;
    modalImg.src = "kepek/autok/" + currentImageSources[index];
    
    dots.forEach((dot, idx) => {
        if (idx === index) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

function changeModalImage(direction) {
    let newIndex;
    if (direction === 'next') {
        newIndex = (currentModalIndex + 1) % currentImageSources.length;
    } else {
        newIndex = (currentModalIndex - 1 + currentImageSources.length) % currentImageSources.length;
    }
    
    showModalImage(newIndex);
}

// Modális háttérre kattintás kezelése (bezárás)
window.onclick = function(event) {
    const modal = document.getElementById("imageModal");
    if (event.target == modal) {
        closeModal();
    }
}

// Billentyűzet nyilak kezelése modális ablakban
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById("imageModal");
    if (modal.style.display === "block") {
        if (event.key === "ArrowLeft") {
            changeModalImage('prev');
        } else if (event.key === "ArrowRight") {
            changeModalImage('next');
        } else if (event.key === "Escape") {
            closeModal();
        }
    }
});