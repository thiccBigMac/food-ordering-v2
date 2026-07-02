let currentIndex = 0;
const slides = document.querySelectorAll('.carousel-slide');
const totalSlides = slides.length;
const track = document.querySelector('.carousel-track');

function updateCarousel() {
    const slideWidth = slides[0].offsetWidth;
    track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
}

// Auto-slide every 5 seconds
setInterval(() => {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateCarousel();
}, 5000);

// Handle resizing to prevent layout bugs
window.addEventListener('resize', () => {
    updateCarousel(); // Recalculate position based on new slide width
});
