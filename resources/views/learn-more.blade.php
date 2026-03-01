@extends('layout.base')

@php($bodyClass = 'landing-body')

@section('title', ($clinicName ?? 'PawCare') . ' - Learn More')

@section('content')
<div class="landing-page">
    <nav>
        <div class="nav-container">
            <div class="logo">
                <span class="paw">🐾</span>
                <h1>{{ $clinicName ?? 'PawCare' }}</h1>
            </div>
            <div class="nav-buttons">
                <a href="{{ url('/') }}" class="btn btn-login">Home</a>
                <a href="{{ url('/login') }}" class="btn btn-register">Login</a>
            </div>
        </div>
    </nav>

    <style>
        .learn-wrap {
            max-width: 1050px;
            margin: 0 auto;
            display: grid;
            gap: 1.25rem;
            padding: 0 0.5rem;
        }

        .learn-hero {
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 18px;
            padding: 1.6rem;
            backdrop-filter: blur(8px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }

        .learn-hero p {
            margin: 0.75rem 0 0;
            color: #4b5563;
            line-height: 1.75;
            max-width: 75ch;
        }

        .learn-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .learn-card {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 16px;
            padding: 1.2rem;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        }

        .learn-card h5 {
            margin: 0 0 0.85rem;
            font-weight: 700;
        }

        .learn-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 0.65rem;
        }

        .learn-list li {
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.65rem 0.75rem;
            color: #374151;
            line-height: 1.5;
        }

        .learn-contact {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .contact-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            padding: 0.8rem;
        }

        .contact-label {
            display: block;
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .contact-value {
            font-weight: 600;
            color: #111827;
            word-break: break-word;
        }

        .cta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin-top: 0.4rem;
        }

        @media (max-width: 900px) {
            .learn-grid,
            .learn-contact {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <main>
        <div class="learn-wrap">
            <section class="learn-hero">
                <h2>
                    Get to Know
                    <span class="gradient-text">{{ $clinicName ?? 'Our Clinic' }}</span>
                </h2>
                <p>
                    We provide compassionate veterinary care focused on preventive health, timely treatment, and reliable support for pet owners. Our goal is to keep every pet healthy through complete, trusted, and accessible services.
                </p>
            </section>

            <div class="learn-grid">
                <section class="learn-card">
                    <h5>Clinic Information</h5>
                    <div class="learn-contact">
                        <div class="contact-item">
                            <span class="contact-label">Clinic Name</span>
                            <div class="contact-value">{{ $clinicSetting->clinic_name ?: ($clinicName ?? 'Not set') }}</div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-label">Email</span>
                            <div class="contact-value">{{ $clinicSetting->clinic_email ?: 'Not set' }}</div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-label">Phone</span>
                            <div class="contact-value">{{ $clinicSetting->clinic_phone ?: 'Not set' }}</div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-label">Timezone</span>
                            <div class="contact-value">{{ $clinicSetting->timezone ?: 'Not set' }}</div>
                        </div>
                        <div class="contact-item" style="grid-column: 1 / -1;">
                            <span class="contact-label">Address</span>
                            <div class="contact-value">{{ $clinicSetting->clinic_address ?: 'Not set' }}</div>
                        </div>
                    </div>
                </section>

                <section class="learn-card">
                    <h5>What We Offer</h5>
                    <ul class="learn-list">
                        <li>Routine checkups and preventive care</li>
                        <li>Vaccination and wellness programs</li>
                        <li>Laboratory and diagnostic support</li>
                        <li>Pharmacy and medication dispensing</li>
                        <li>Grooming, boarding, and surgery services</li>
                    </ul>
                </section>
            </div>

            <section class="learn-card">
                <h5>Ready to Start?</h5>
                <p style="margin: 0; color: #4b5563; line-height: 1.7;">
                    Create an account to manage appointments, view records, and keep your pet care journey organized in one place.
                </p>
                <div class="cta-row">
                    <a href="{{ url('/register') }}" class="btn btn-primary">
                        Register Now
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
                </div>
            </section>
        </div>
    </main>
</div>
@endsection
