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

@push('scripts')
<script>
    const petIcons = [
        `<svg viewBox="0 0 200 200" class="pet-icon">
            <circle cx="100" cy="100" r="95" fill="#E9D5FF" opacity="0.3"/>
            <path d="M100 140 Q85 155 70 150 Q60 145 62 135 Q65 120 75 115 Q90 105 100 110 Q110 105 125 115 Q135 120 138 135 Q140 145 130 150 Q115 155 100 140Z" fill="#A78BFA"/>
            <circle cx="85" cy="120" r="8" fill="#7C3AED"/>
            <circle cx="115" cy="120" r="8" fill="#7C3AED"/>
            <path d="M95 130 Q100 135 105 130" stroke="#7C3AED" fill="none" stroke-width="3"/>
            <ellipse cx="60" cy="80" rx="20" ry="30" fill="#A78BFA"/>
            <ellipse cx="140" cy="80" rx="20" ry="30" fill="#A78BFA"/>
            <ellipse cx="45" cy="65" rx="15" ry="20" fill="#C4B5FD"/>
            <ellipse cx="155" cy="65" rx="15" ry="20" fill="#C4B5FD"/>
        </svg>`,
        `<svg viewBox="0 0 200 200" class="pet-icon">
            <circle cx="100" cy="100" r="95" fill="#DDD6FE" opacity="0.3"/>
            <path d="M65 60 L55 30 L75 50 Z" fill="#A78BFA"/>
            <path d="M135 60 L145 30 L125 50 Z" fill="#A78BFA"/>
            <circle cx="100" cy="100" r="45" fill="#C4B5FD"/>
            <circle cx="85" cy="95" r="8" fill="#7C3AED"/>
            <circle cx="115" cy="95" r="8" fill="#7C3AED"/>
            <path d="M90 105 L95 110 L100 108 L105 110 L110 105" stroke="#7C3AED" fill="none" stroke-width="2"/>
            <path d="M70 110 L85 108" stroke="#A78BFA" stroke-width="2"/>
            <path d="M130 110 L115 108" stroke="#A78BFA" stroke-width="2"/>
        </svg>`,
        `<svg viewBox="0 0 200 200" class="pet-icon">
            <circle cx="100" cy="100" r="95" fill="#E9D5FF" opacity="0.3"/>
            <ellipse cx="100" cy="110" rx="35" ry="40" fill="#C4B5FD"/>
            <circle cx="90" cy="90" r="25" fill="#DDD6FE"/>
            <circle cx="85" cy="88" r="6" fill="#7C3AED"/>
            <path d="M95 95 L110 100 L95 105" fill="#F59E0B"/>
            <ellipse cx="60" cy="110" rx="30" ry="15" fill="#A78BFA" transform="rotate(-20 60 110)"/>
            <ellipse cx="140" cy="110" rx="30" ry="15" fill="#A78BFA" transform="rotate(20 140 110)"/>
            <path d="M100 145 L95 155 M100 145 L105 155" stroke="#A78BFA" stroke-width="3" fill="none"/>
        </svg>`,
        `<svg viewBox="0 0 200 200" class="pet-icon">
            <circle cx="100" cy="100" r="95" fill="#DDD6FE" opacity="0.3"/>
            <ellipse cx="75" cy="60" rx="12" ry="45" fill="#C4B5FD"/>
            <ellipse cx="125" cy="60" rx="12" ry="45" fill="#C4B5FD"/>
            <circle cx="100" cy="110" r="40" fill="#DDD6FE"/>
            <circle cx="90" cy="105" r="6" fill="#7C3AED"/>
            <circle cx="110" cy="105" r="6" fill="#7C3AED"/>
            <path d="M95 115 L100 120 L105 115" stroke="#7C3AED" fill="none" stroke-width="2"/>
            <circle cx="100" cy="125" r="3" fill="#F59E0B"/>
        </svg>`,
        `<svg viewBox="0 0 200 200" class="pet-icon">
            <circle cx="100" cy="100" r="95" fill="#E9D5FF" opacity="0.3"/>
            <ellipse cx="110" cy="100" rx="45" ry="30" fill="#A78BFA"/>
            <path d="M65 100 L45 85 L45 115 Z" fill="#C4B5FD"/>
            <path d="M150 90 L170 80 L165 100 L170 120 L150 110 Z" fill="#DDD6FE"/>
            <circle cx="125" cy="95" r="6" fill="#7C3AED"/>
            <path d="M100 85 Q105 80 110 85" stroke="#DDD6FE" fill="none" stroke-width="2"/>
            <path d="M100 95 Q105 90 110 95" stroke="#DDD6FE" fill="none" stroke-width="2"/>
        </svg>`
    ];

    let currentIndex = 0;
    const carousel = document.getElementById('carousel');
    const indicatorsContainer = document.getElementById('indicators');

    petIcons.forEach((icon) => {
        const item = document.createElement('div');
        item.className = 'carousel-item';
        item.innerHTML = `<div class="carousel-circle">${icon}</div>`;
        carousel.appendChild(item);
    });

    petIcons.forEach((_, index) => {
        const indicator = document.createElement('button');
        indicator.className = 'indicator';
        if (index === 0) indicator.classList.add('active');
        indicator.addEventListener('click', () => goToSlide(index));
        indicatorsContainer.appendChild(indicator);
    });

    function updateCarousel() {
        const items = document.querySelectorAll('.carousel-item');
        const indicators = document.querySelectorAll('.indicator');

        items.forEach((item, index) => {
            const position = (index - currentIndex + petIcons.length) % petIcons.length;
            const isActive = position === 0;

            item.style.transform = `translateX(${position * 100}%) scale(${isActive ? 1 : 0.8})`;
            item.style.opacity = isActive ? '1' : '0';
            item.style.zIndex = isActive ? '10' : '0';
        });

        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentIndex);
        });
    }

    function goToSlide(index) {
        currentIndex = index;
        updateCarousel();
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % petIcons.length;
        updateCarousel();
    }

    setInterval(nextSlide, 3000);
    updateCarousel();
</script>
@endpush
