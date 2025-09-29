document.querySelectorAll('[id^="btnGaleria-"]').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.id.split('-')[1];
        const carrossel = document.getElementById('carrossel-' + id);
        carrossel.style.display = 'block';

        let slides = carrossel.querySelectorAll('.carrossel-slide');
        let current = 0;

        const showSlide = index => {
            slides.forEach((s, i) => s.style.display = i === index ? 'block' : 'none');
        };

        carrossel.querySelector('.prev-btn').onclick = () => {
            current = (current - 1 + slides.length) % slides.length;
            showSlide(current);
        };

        carrossel.querySelector('.next-btn').onclick = () => {
            current = (current + 1) % slides.length;
            showSlide(current);
        };

        carrossel.querySelector('.close-btn').onclick = () => {
            carrossel.style.display = 'none';
        };
    });
});
