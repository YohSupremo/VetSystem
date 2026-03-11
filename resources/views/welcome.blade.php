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

/* ── Tablet (≤ 1024px) ─────────────────────────────────── */
@media (max-width: 1024px) {
    .carousel-container {
        transform: none !important;
        margin: 0 auto;
    }
}

/* ── Mobile (≤ 768px) ──────────────────────────────────── */
@media (max-width: 768px) {
    /* ─ Navigation ─ */
    .nav-container {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        gap: 10px;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo .paw { font-size: 1.8rem; }
    .logo h1 { font-size: 1.4rem; }

    .nav-buttons {
        display: flex;
        gap: 8px;
    }

    .nav-buttons .btn {
        padding: 8px 16px;
        font-size: 13px;
        min-width: auto;
    }

    /* ─ Content Grid ─ */
    .content-grid {
        grid-template-columns: 1fr !important;
        gap: 24px;
        padding: 20px;
    }

    /* ─ Hero Text ─ */
    .content-left {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 100%;
        order: 1;
    }

    .content-left h2 {
        font-size: 2rem;
        line-height: 1.25;
    }

    .content-left p {
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* ─ CTA Buttons ─ */
    .cta-buttons {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 12px;
        width: 100%;
        justify-content: center;
    }

    .cta-buttons .btn {
        flex: 1;
        min-width: 130px;
        text-align: center;
        padding: 14px 20px;
        font-size: 14px;
        justify-content: center;
    }

    /* ─ Carousel ─ */
    .carousel-container {
        order: 0;
        width: 60vw;
        max-width: 280px;
        margin: 0 auto;
        padding: 0;
        transform: none !important;
    }

    /* ─ Stats ─ */
    .stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 12px;
        margin: 0;
        padding-top: 0.5rem;
    }

    .stat-item {
        text-align: center;
        padding: 14px 8px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(12px);
        border-radius: 16px;
        border: 1px solid rgba(147,51,234,0.1);
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .stat-label {
        font-size: 0.7rem;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
}

/* ── Small phones (≤ 480px) ────────────────────────────── */
@media (max-width: 480px) {
    .nav-container {
        padding: 10px 14px;
    }

    .logo .paw { font-size: 1.5rem; }
    .logo h1 { font-size: 1.2rem; }

    .nav-buttons .btn {
        padding: 7px 12px;
        font-size: 12px;
    }

    .content-grid {
        padding: 14px;
        gap: 18px;
    }

    .content-left h2 {
        font-size: 1.65rem;
    }

    .content-left p {
        font-size: 0.88rem;
    }

    .cta-buttons {
        flex-direction: column;
        gap: 10px;
    }

    .cta-buttons .btn {
        padding: 12px;
        font-size: 13px;
    }

    .carousel-container {
        width: 55vw;
        max-width: 220px;
    }

    .stat-number {
        font-size: 1.25rem;
    }

    .stat-label {
        font-size: 0.65rem;
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
                    <a href="/register" class="btn btn-cta-primary">
                        <span>Get Started</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
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