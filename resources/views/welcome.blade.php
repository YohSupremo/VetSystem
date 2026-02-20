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
<script type="application/json" id="carousel-data">@json($carouselImages ?? [])</script>
@endsection

@push('styles')
<style>
    .carousel-slide-track {
        display: flex;
        width: 100%;
        height: 100%;
        position: relative;
    }
    .carousel-item {
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        pointer-events: none;
    }
    .carousel-item.active {
        opacity: 1;
        pointer-events: auto;
    }
    .carousel-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }
    .carousel-circle {
        width: 100%;
        height: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
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
    (function() {
        var dataEl = document.getElementById('carousel-data');
        var carouselImages = [];
        if (dataEl && dataEl.textContent) {
            try { carouselImages = JSON.parse(dataEl.textContent); } catch (e) {
                console.error('Failed to parse carousel data:', e);
            }
        }
        if (!Array.isArray(carouselImages)) carouselImages = [];

        var fallbackIcons = ['🐕', '🐈', '🐇', '🦜', '🐠'];
        var hasImages = carouselImages.length > 0;
        var sources = hasImages ? carouselImages : fallbackIcons;

        var currentIndex = 0;
        var carousel = document.getElementById('carousel');
        var indicatorsContainer = document.getElementById('indicators');
        if (!carousel || !indicatorsContainer) {
            console.error('Carousel elements not found');
            return;
        }

        var track = document.createElement('div');
        track.className = 'carousel-slide-track';
        carousel.appendChild(track);

        sources.forEach(function(source, index) {
            var item = document.createElement('div');
            item.className = 'carousel-item' + (index === 0 ? ' active' : '');
            item.setAttribute('data-index', index);

            var circle = document.createElement('div');
            circle.className = 'carousel-circle';

            if (hasImages) {
                var img = document.createElement('img');
                img.src = source;
                img.alt = 'Pet ' + (index + 1);
                img.className = 'carousel-image';
                img.onload = function() {
                    console.log('Image loaded:', source);
                };
                img.onerror = function() {
                    console.error('Image failed to load:', source);
                    circle.innerHTML = '<div class="image-placeholder">' + fallbackIcons[index % fallbackIcons.length] + '</div>';
                };
                circle.appendChild(img);
            } else {
                circle.innerHTML = '<div class="image-placeholder">' + source + '</div>';
            }
            item.appendChild(circle);
            track.appendChild(item);
        });

        sources.forEach(function(_, index) {
            var indicator = document.createElement('button');
            indicator.className = 'indicator' + (index === 0 ? ' active' : '');
            indicator.setAttribute('aria-label', 'Go to slide ' + (index + 1));
            indicator.addEventListener('click', function() { goToSlide(index); });
            indicatorsContainer.appendChild(indicator);
        });

        function updateCarousel() {
            var items = track.querySelectorAll('.carousel-item');
            var indicators = indicatorsContainer.querySelectorAll('.indicator');
            items.forEach(function(el, i) {
                el.classList.toggle('active', i === currentIndex);
            });
            indicators.forEach(function(el, i) {
                el.classList.toggle('active', i === currentIndex);
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

        var autoRotate = null;
        if (sources.length > 1) {
            autoRotate = setInterval(nextSlide, 3000);
        }
        carousel.addEventListener('mouseenter', function() {
            if (autoRotate) { clearInterval(autoRotate); autoRotate = null; }
        });
        carousel.addEventListener('mouseleave', function() {
            if (sources.length > 1 && !autoRotate) autoRotate = setInterval(nextSlide, 3000);
        });

        updateCarousel();

        document.addEventListener('keydown', function(e) {
            if (sources.length <= 1) return;
            if (e.key === 'ArrowLeft') prevSlide();
            if (e.key === 'ArrowRight') nextSlide();
        });
    })();
</script>
@endpush