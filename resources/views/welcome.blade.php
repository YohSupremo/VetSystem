@extends('layout.base')

@php($bodyClass = 'landing-body')

@section('title', ($clinicName ?? 'PawCare') . ' - Veterinary Care')

@section('content')
<style>
/* Base landing page styles */
.landing-page {
    min-height: 100vh;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    padding: 40px;
}

/* Force 3 columns for mobile */
@media (max-width: 768px) {
    .nav-container {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
    }
    
    .logo {
        display: flex;
        align-items: center;
        gap: 15px;
        justify-content: center !important;
        text-align: center !important;
    }
    
    .nav-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        width: 100%;
        justify-content: center !important;
        align-items: center !important;
        text-align: center !important;
    }
    
    /* Center main content area */
    main {
        text-align: center !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .nav-buttons .btn {
        flex: 1;
        min-width: 120px;
        text-align: center;
        padding: 12px 16px;
        font-size: 14px;
    }
    
    .content-grid {
        grid-template-columns: 1fr !important;
        gap: 2vw;
        padding: 3vw 3vw 3vw 1vw;
        margin-left: -2vw;
        align-items: stretch;
        justify-items: stretch;
        text-align: left;
    }
    
    /* Content follows navigation alignment */
    .content-left {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        text-align: left;
        width: 100%;
    }
    
    .content-left > div {
        text-align: left;
        align-items: flex-start;
        justify-content: flex-start;
    }
    
    .content-left h2,
    .content-left p {
        text-align: left;
        margin-left: 0;
        margin-right: 0;
    }
    
    .cta-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        width: 100%;
        justify-content: center;
        align-items: center;
    }
    
    .cta-buttons .btn {
        flex: 1;
        min-width: 140px;
        text-align: center;
        padding: 15px;
    }
    
    /* Auto-adjusting carousel and stats positioning */
    .carousel-container {
        position: relative;
        width: 100%;
        max-width: min(50vw, 20rem);
        margin: 3vw auto 3vw -3vw;
        transform: none !important;
    }
    
    .stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 2vw;
        margin-top: 4vw;
        margin-left: 3vw;
        margin-right: -3vw;
    }
    
    .stat-item {
        text-align: center;
        padding: 15px 10px;
    }
    
    .stat-number {
        font-size: 24px;
    }
    
    .stat-label {
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .nav-container {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
    }
    
    .logo {
        display: flex;
        align-items: center;
        gap: 15px;
        justify-content: center !important;
        text-align: center !important;
    }
    
    .nav-buttons {
        flex-direction: column;
        gap: 8px;
        justify-content: center !important;
        align-items: center !important;
        text-align: center !important;
    }
    
    .nav-buttons .btn {
        font-size: 13px;
        padding: 10px 14px;
        min-width: auto;
    }
    
    .content-grid {
        grid-template-columns: 1fr !important;
        padding: 3vw 3vw 3vw 1vw;
        margin-left: -2vw;
        gap: 2vw;
        align-items: stretch;
        justify-items: stretch;
        text-align: left;
    }
    
    /* Content follows navigation alignment */
    .content-left {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        text-align: left;
        width: 100%;
    }
    
    .content-left > div {
        text-align: left;
        align-items: flex-start;
        justify-content: flex-start;
    }
    
    .content-left h2,
    .content-left p {
        text-align: left;
        margin-left: 0;
        margin-right: 0;
    }
    
    .cta-buttons {
        flex-direction: column;
        gap: 12px;
        justify-content: center;
        align-items: center;
    }
    
    .cta-buttons .btn {
        padding: 12px;
        font-size: 14px;
        min-width: auto;
    }
    
    .stats {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 2vw;
        margin-top: 4vw;
        margin-left: 2.5vw;
        margin-right: -2.5vw;
    }
    
    /* Auto-adjusting carousel */
    .carousel-container {
        position: relative;
        width: 100%;
        max-width: min(55vw, 20rem);
        margin: 3vw auto 3vw -2.5vw;
        transform: none !important;
    }
    
    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 12px 8px;
    }
    
    .stat-number {
        font-size: 20px;
    }
    
    .stat-label {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .content-grid {
        grid-template-columns: 1fr !important;
        padding: 3vw 3vw 3vw 1vw;
        margin-left: -2vw;
        gap: 2vw;
        align-items: stretch;
        justify-items: stretch;
        text-align: left;
    }
    
    /* Content follows navigation alignment */
    .content-left {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        text-align: left;
        width: 100%;
    }
    
    .content-left > div {
        text-align: left;
        align-items: flex-start;
        justify-content: flex-start;
    }
    
    .content-left h2,
    .content-left p {
        text-align: left;
        margin-left: 0;
        margin-right: 0;
    }
    
    .stats {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 2vw;
        margin-top: 4vw;
        margin-left: 2vw;
        margin-right: -2vw;
    }
    
    /* Auto-adjusting carousel */
    .carousel-container {
        position: relative;
        width: 100%;
        max-width: min(60vw, 20rem);
        margin: 3vw auto 3vw -2vw;
        transform: none !important;
    }
    
    .stat-item {
        padding: 10px 6px;
    }
    
    .stat-number {
        font-size: 18px;
    }
    
    .stat-label {
        font-size: 10px;
    }
    
    /* Center carousel on mobile */
    .carousel-container {
        position: relative;
        width: 100%;
        max-width: 20rem;
        margin: 0 auto;
        transform: none !important;
    }
}
</style>
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