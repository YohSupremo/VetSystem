@extends('layout.base')

@php($bodyClass = 'landing-body')

@section('title', ($clinicName ?? 'PawCare') . ' - Veterinary Care')

@section('content')
<div class="landing-page">
    <nav>
        <div class="nav-container">
            <div class="logo">
                <span class="paw">🐾</span>
                <h1>{{ $clinicName ?? 'PawCare' }}</h1>
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
                    <a href="{{ route('learn-more') }}" class="btn btn-secondary">Learn More</a>
                </div>

                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number">{{ number_format($landingStats['pets'] ?? 0) }}</div>
                        <div class="stat-label">Happy Pets</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ number_format($landingStats['veterinarians'] ?? 0) }}</div>
                        <div class="stat-label">Expert Vets</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ number_format($landingStats['appointments'] ?? 0) }}</div>
                        <div class="stat-label">Appointments</div>
                    </div>
                </div>
            </div>

            <div class="carousel-container">
                <div class="carousel-wrapper">
                    <div class="carousel-track">
                        <div class="carousel-slide active">
                            <img src="/images/carousel/pet-1.png" alt="Pet 1" class="carousel-image">
                        </div>
                        <div class="carousel-slide">
                            <img src="/images/carousel/pet-2.png" alt="Pet 2" class="carousel-image">
                        </div>
                        <div class="carousel-slide">
                            <img src="/images/carousel/pet-3.png" alt="Pet 3" class="carousel-image">
                        </div>
                        <div class="carousel-slide">
                            <img src="/images/carousel/pet-4.png" alt="Pet 4" class="carousel-image">
                        </div>
                        <div class="carousel-slide">
                            <img src="/images/carousel/pet-5.png" alt="Pet 5" class="carousel-image">
                        </div>
                    </div>
                                        <div class="carousel-indicators">
                        <span class="indicator active" onclick="goToSlide(0)"></span>
                        <span class="indicator" onclick="goToSlide(1)"></span>
                        <span class="indicator" onclick="goToSlide(2)"></span>
                        <span class="indicator" onclick="goToSlide(3)"></span>
                        <span class="indicator" onclick="goToSlide(4)"></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.indicator');
const totalSlides = slides.length;

function showSlide(index) {
    // Hide all slides
    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(indicator => indicator.classList.remove('active'));
    
    // Show current slide
    slides[index].classList.add('active');
    indicators[index].classList.add('active');
}

function changeSlide(direction) {
    currentSlide += direction;
    
    if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    } else if (currentSlide >= totalSlides) {
        currentSlide = 0;
    }
    
    showSlide(currentSlide);
}

function goToSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
}

// Auto-advance carousel
function autoAdvanceCarousel() {
    changeSlide(1);
}

// Start auto-advance
setInterval(autoAdvanceCarousel, 4000);

// Initialize
showSlide(0);
</script>

@endsection