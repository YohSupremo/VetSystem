@extends('layout.base')

@php($bodyClass = 'landing-body')

@section('title', 'PawCare - Veterinary Care')

@section('content')
<div class="landing-page">
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <nav>
        <div class="nav-container">
            <div class="logo">
                <span class="paw">🐾</span>
                <h1>PawCare</h1>
            </div>
            <div class="nav-buttons">
                <a href="/login" class="btn btn-login">Login</a>
                <a href="/register" class="btn btn-register">Register</a>
            </div>
        </div>
    </nav>

    <main>
        <div class="content-grid">
            <div class="content-left">
                <div>
                    <h2>
                        Compassionate Care for Your
                        <span class="gradient-text">Beloved Pets</span>
                    </h2>
                    <p>
                        Providing exceptional veterinary services with love, expertise, and dedication to keep your furry friends healthy and happy.
                    </p>
                </div>

                <div class="cta-buttons">
                    <a href="/register" class="btn btn-primary">
                        Get Started
                        <span>&rarr;</span>
                    </a>
                    <a href="#services" class="btn btn-secondary">Learn More</a>
                </div>

                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Happy Pets</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Expert Vets</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                </div>
            </div>

            <div class="carousel-container">
                <div class="carousel-wrapper" id="carousel"></div>

                <div class="deco-1"></div>
                <div class="deco-2"></div>

                <div class="indicators" id="indicators"></div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('styles')
<style>
    .carousel-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .carousel-circle {
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #E9D5FF 0%, #C4B5FD 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 64px;
        border-radius: 50%;
    }
</style>
@endpush

@push('scripts')
<script>
    // Define your carousel images here
    const carouselImages = @json($carouselImages ?? []);
    const fallbackIcons = ['🐕', '🐈', '🐇', '🦜', '🐠'];

    const hasImages = carouselImages.length > 0;
    const sources = hasImages ? carouselImages : fallbackIcons;

    let currentIndex = 0;
    const carousel = document.getElementById('carousel');
    const indicatorsContainer = document.getElementById('indicators');

    sources.forEach((source, index) => {
        const item = document.createElement('div');
        item.className = 'carousel-item';
        item.style.transform = `rotateY(${index * 72}deg) translateZ(200px)`;

        if (hasImages) {
            const circle = document.createElement('div');
            circle.className = 'carousel-circle';

            const img = document.createElement('img');
            img.src = source;
            img.alt = `Pet ${index + 1}`;
            img.className = 'carousel-image';

            img.onerror = function() {
                circle.innerHTML = `<div class="image-placeholder">${fallbackIcons[index % fallbackIcons.length]}</div>`;
            };

            circle.appendChild(img);
            item.appendChild(circle);
        } else {
            item.innerHTML = `<div class="carousel-circle"><div class="image-placeholder">${source}</div></div>`;
        }

        carousel.appendChild(item);
    });

    sources.forEach((_, index) => {
        const indicator = document.createElement('button');
        indicator.className = 'indicator';
        if (index === 0) indicator.classList.add('active');
        indicator.addEventListener('click', () => goToSlide(index));
        indicator.setAttribute('aria-label', `Go to slide ${index + 1}`);
        indicatorsContainer.appendChild(indicator);
    });

    function updateCarousel() {
        const angle = -currentIndex * 72;
        carousel.style.transform = `rotateY(${angle}deg)`;

        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentIndex);
        });
    }

    function goToSlide(index) {
        currentIndex = index;
        updateCarousel();
    }

    function nextSlide() {
        if (sources.length <= 1) return;
        currentIndex = (currentIndex + 1) % sources.length;
        updateCarousel();
    }

    function prevSlide() {
        if (sources.length <= 1) return;
        currentIndex = (currentIndex - 1 + sources.length) % sources.length;
        updateCarousel();
    }

    // Auto-rotate carousel every 3 seconds
    let autoRotate = null;
    if (sources.length > 1) {
        autoRotate = setInterval(nextSlide, 3000);
    }

    // Pause auto-rotate on hover
    carousel.addEventListener('mouseenter', () => {
        if (autoRotate) {
            clearInterval(autoRotate);
            autoRotate = null;
        }
    });

    carousel.addEventListener('mouseleave', () => {
        if (sources.length > 1 && !autoRotate) {
            autoRotate = setInterval(nextSlide, 3000);
        }
    });

    // Initialize carousel
    updateCarousel();

    // Optional: Add keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (sources.length <= 1) return;
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
    });
</script>
@endpush