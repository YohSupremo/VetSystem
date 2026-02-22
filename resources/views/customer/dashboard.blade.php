@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Customer Dashboard - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
}

.logo-section .paw-icon {
    font-size: 2.5rem;
    animation: bounce 2s infinite;
}

.logo-section h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.welcome-text {
    color: var(--primary-purple);
    font-weight: 500;
    font-size: 1.1rem;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.logout-btn {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    color: var(--primary-purple);
    text-decoration: none;
    font-weight: 500;
    padding: 0.6rem 1.2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    transition: var(--transition-smooth);
}

.logout-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.3);
    color: var(--primary-purple);
    border-color: rgba(255, 255, 255, 0.3);
}

.customer-main {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Enhanced Stats Cards with Glassmorphism */
.stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 2rem;
    transition: var(--transition-smooth);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
}

.stat-icon {
    font-size: 3.5rem;
    width: 90px;
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2rem;
    position: relative;
    transition: all 0.3s ease;
}

.stat-icon::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 2rem;
    padding: 3px;
    background: linear-gradient(135deg, rgba(255,255,255,0.5), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover .stat-icon::after {
    opacity: 1;
}

.stat-card.pets .stat-icon {
    background: linear-gradient(135deg, #10b981, #34d399);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}

.stat-card.appointments .stat-icon {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
}

.stat-card.health .stat-icon {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.stat-content h3 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.stat-content p {
    color: #6B7280;
    font-weight: 500;
    font-size: 1rem;
}

/* Enhanced Quick Actions with Modern Design */
.quick-actions h2 {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 2.5rem;
    text-align: center;
    position: relative;
}

.quick-actions h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
    border-radius: 2px;
}

.action-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
    padding: 3rem 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-decoration: none;
    color: inherit;
    transition: var(--transition-smooth);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
    height: 100%;
    min-height: 300px;
    justify-content: center;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(167, 139, 250, 0.05), rgba(236, 72, 153, 0.05));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.action-card:hover::before {
    opacity: 1;
}

.action-card:hover {
    transform: translateY(-12px) scale(1.03);
    box-shadow: 0 25px 70px rgba(31, 38, 135, 0.45);
    text-decoration: none;
    color: inherit;
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.25);
}

.action-icon {
    font-size: 3.5rem;
    margin-bottom: 2rem;
    width: 90px;
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2rem;
    background: linear-gradient(135deg, var(--light-purple), var(--secondary-purple));
    box-shadow: 0 10px 25px rgba(168, 85, 247, 0.25);
    margin: 0 auto 2rem;
    position: relative;
    transition: all 0.3s ease;
}

.action-icon::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 2rem;
    padding: 3px;
    background: linear-gradient(135deg, rgba(255,255,255,0.6), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.action-card:hover .action-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 15px 35px rgba(168, 85, 247, 0.35);
}

.action-card:hover .action-icon::after {
    opacity: 1;
}

.action-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.75rem;
}

.action-card p {
    color: #6B7280;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Section Headers */
.section-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-add, .btn-view-all {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    text-decoration: none;
    padding: 0.75rem 1.5rem;
    border-radius: 1rem;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
}

.btn-add:hover, .btn-view-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    text-decoration: none;
    color: white;
}

/* Enhanced Pet and Appointment Cards with Glassmorphism */
.pet-card, .appointment-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 1.5rem;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: var(--transition-smooth);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
}

.pet-card::before, .appointment-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pet-card:hover::before, .appointment-card:hover::before {
    opacity: 1;
}

.pet-card:hover, .appointment-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
}

.pet-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--light-purple);
    box-shadow: 0 6px 20px rgba(167, 139, 250, 0.25);
    position: relative;
    transition: all 0.3s ease;
}

.pet-avatar::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    padding: 3px;
    background: linear-gradient(135deg, rgba(255,255,255,0.6), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pet-card:hover .pet-avatar {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(167, 139, 250, 0.35);
}

.pet-card:hover .pet-avatar::after {
    opacity: 1;
}

.pet-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.default-avatar {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--light-purple);
    font-size: 1.5rem;
}

.pet-details h3, .appointment-details h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.pet-details p, .appointment-details p {
    color: #6B7280;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.appointment-date {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border-radius: 1.25rem;
    padding: 1.25rem;
    min-width: 80px;
    box-shadow: 0 8px 20px rgba(147, 51, 234, 0.3);
    position: relative;
    transition: all 0.3s ease;
}

.appointment-date::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 1.25rem;
    padding: 2px;
    background: linear-gradient(135deg, rgba(255,255,255,0.3), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.appointment-card:hover .appointment-date {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(147, 51, 234, 0.4);
}

.appointment-card:hover .appointment-date::after {
    opacity: 1;
}

.date-day {
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1;
}

.date-month {
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    margin-top: 0.25rem;
}

.btn-view {
    background: transparent;
    color: var(--primary-purple);
    border: 2px solid var(--light-purple);
    padding: 0.75rem 1.5rem;
    border-radius: 1.25rem;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    display: inline-block;
}

.btn-view::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--light-purple), var(--secondary-purple));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 1.25rem;
    z-index: -1;
}

.btn-view span {
    position: relative;
    z-index: 1;
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(147, 51, 234, 0.2);
    text-decoration: none;
    color: white;
    border-color: var(--secondary-purple);
}

.btn-view:hover::before {
    opacity: 1;
}

/* Enhanced Empty States with Glassmorphism */
.empty-state {
    text-align: center;
    padding: 3.5rem 2rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
    transition: var(--transition-smooth);
}

.empty-state::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.empty-state:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 50px rgba(31, 38, 135, 0.35);
    background: rgba(255, 255, 255, 0.2);
}

.empty-icon {
    font-size: 4.5rem;
    margin-bottom: 2rem;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 4px 10px rgba(147, 51, 234, 0.2));
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #6B7280;
    margin-bottom: 2rem;
    font-size: 1rem;
    line-height: 1.6;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    text-decoration: none;
    padding: 1rem 2.5rem;
    border-radius: 1.25rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-block;
    box-shadow: 0 6px 20px rgba(147, 51, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.btn-primary span {
    position: relative;
    z-index: 1;
}

.btn-primary:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 12px 30px rgba(147, 51, 234, 0.4);
    text-decoration: none;
    color: white;
}

.btn-primary:hover::before {
    opacity: 1;
}

.btn-primary:active {
    transform: translateY(-1px) scale(1.02);
}

/* Enhanced Recent Activity with Glassmorphism */
.recent-activity {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
}

.recent-activity::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.recent-activity h2 {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 2rem;
    position: relative;
}

.recent-activity h2::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
    border-radius: 2px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 1.5rem;
    transition: var(--transition-smooth);
    border: 1px solid rgba(255, 255, 255, 0.15);
    position: relative;
    overflow: hidden;
}

.activity-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.activity-item:hover::before {
    opacity: 1;
}

.activity-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px) translateX(4px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.25);
}

.activity-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.3rem;
    box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
    position: relative;
    transition: all 0.3s ease;
}

.activity-icon::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 50%;
    padding: 2px;
    background: linear-gradient(135deg, rgba(255,255,255,0.4), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.activity-item:hover .activity-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
}

.activity-item:hover .activity-icon::after {
    opacity: 1;
}

.activity-content h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.activity-content p {
    color: #6B7280;
    font-size: 0.9rem;
}

/* Status Badges */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.status-badge.confirmed {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.status-badge.in_progress {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}

/* Enhanced Mobile Quick Actions Container with Glassmorphism */
.mobile-quick-actions {
    display: none;
}

.mobile-actions-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
    padding: 2rem;
}

.mobile-actions-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.mobile-actions-container h2 {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1.5rem;
    text-align: center;
    position: relative;
}

.mobile-actions-container h2::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
    border-radius: 2px;
}

.mobile-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1.5rem;
}

.mobile-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: inherit;
    transition: var(--transition-smooth);
    padding: 1.25rem;
    border-radius: 1.25rem;
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.mobile-action-item::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 1.25rem;
    background: linear-gradient(135deg, rgba(167, 139, 250, 0.1), rgba(236, 72, 153, 0.1));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.mobile-action-item:hover::before {
    opacity: 1;
}

.mobile-action-item:hover {
    transform: translateY(-4px) scale(1.05);
    text-decoration: none;
    color: inherit;
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.25);
    background: rgba(255, 255, 255, 0.15);
}

.mobile-action-icon {
    width: 65px;
    height: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1.25rem;
    background: linear-gradient(135deg, var(--light-purple), var(--secondary-purple));
    box-shadow: 0 6px 15px rgba(168, 85, 247, 0.25);
    margin-bottom: 1rem;
    font-size: 1.75rem;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.mobile-action-icon::after {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 1.25rem;
    padding: 2px;
    background: linear-gradient(135deg, rgba(255,255,255,0.6), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.mobile-action-item:hover .mobile-action-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 10px 25px rgba(168, 85, 247, 0.35);
}

.mobile-action-item:hover .mobile-action-icon::after {
    opacity: 1;
}

.mobile-action-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--primary-purple);
    text-align: center;
    line-height: 1.2;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.mobile-action-item:hover .mobile-action-label {
    color: var(--secondary-purple);
    transform: translateY(-1px);
}

.mobile-action-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: linear-gradient(135deg, var(--pink), #ec4899);
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    box-shadow: 0 3px 10px rgba(236, 72, 153, 0.4);
    z-index: 2;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 3px 10px rgba(236, 72, 153, 0.4);
    }
    50% {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(236, 72, 153, 0.6);
    }
}

/* Enhanced Responsive Design */
@media (max-width: 1024px) {
    .customer-main {
        padding: 1.5rem;
    }
    
    .customer-header {
        padding: 1.5rem 1rem;
    }
    
    .greeting {
        font-size: 2rem;
    }
    
    .subtitle {
        font-size: 1rem;
    }
    
    .stats-section {
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        padding: 1.5rem;
        min-height: 120px;
    }
    
    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }
    
    .stat-content h3 {
        font-size: 1.5rem;
    }
    
    .stat-content p {
        font-size: 0.875rem;
    }
    
    .action-card {
        padding: 1.5rem;
        min-height: 140px;
    }
    
    .action-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }
    
    .action-title {
        font-size: 1rem;
    }
    
    .action-description {
        font-size: 0.875rem;
    }
    
    .pet-card {
        padding: 1.5rem;
    }
    
    .pet-avatar {
        width: 60px;
        height: 60px;
    }
    
    .pet-info h4 {
        font-size: 1rem;
    }
    
    .pet-info p {
        font-size: 0.875rem;
    }
    
    .appointment-card {
        padding: 1.5rem;
    }
    
    .appointment-time h4 {
        font-size: 1rem;
    }
    
    .appointment-time p {
        font-size: 0.875rem;
    }
    
    .appointment-details h4 {
        font-size: 1rem;
    }
    
    .appointment-details p {
        font-size: 0.875rem;
    }
    
    .activity-item {
        padding: 1rem;
    }
    
    .activity-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .activity-title {
        font-size: 0.875rem;
    }
    
    .activity-description {
        font-size: 0.75rem;
    }
    
    .activity-time {
        font-size: 0.75rem;
    }
}

@media (max-width: 768px) {
    .customer-main {
        padding: 1rem;
    }
    
    .customer-header {
        padding: 1rem;
        text-align: center;
    }
    
    .greeting {
        font-size: 1.75rem;
    }
    
    .subtitle {
        font-size: 0.9rem;
    }
    
    .stats-section {
        margin-bottom: 1rem;
    }
    
    .stat-card {
        padding: 1.25rem;
        min-height: 100px;
        margin-bottom: 1rem;
    }
    
    .stat-icon {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .stat-content h3 {
        font-size: 1.25rem;
    }
    
    .stat-content p {
        font-size: 0.75rem;
    }
    
    .quick-actions {
        margin-bottom: 1rem;
    }
    
    .quick-actions h2 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .desktop-actions {
        display: none;
    }
    
    .mobile-quick-actions {
        display: block;
    }
    
    .mobile-actions-container {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        padding: 1rem;
    }
    
    .mobile-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .action-card {
        padding: 1rem;
        min-height: 100px;
        text-align: center;
    }
    
    .action-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .action-title {
        font-size: 0.875rem;
    }
    
    .action-description {
        display: none;
    }
    
    .pets-section h2,
    .appointments-section h2,
    .activity-section h2 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .pets-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }
    
    .pet-card {
        padding: 1.25rem;
    }
    
    .pet-avatar {
        width: 50px;
        height: 50px;
    }
    
    .pet-info h4 {
        font-size: 0.9rem;
    }
    
    .pet-info p {
        font-size: 0.75rem;
    }
    
    .appointments-list {
        gap: 1rem;
    }
    
    .appointment-card {
        padding: 1.25rem;
    }
    
    .appointment-time {
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .appointment-time h4 {
        font-size: 0.9rem;
    }
    
    .appointment-time p {
        font-size: 0.75rem;
    }
    
    .appointment-details h4 {
        font-size: 0.9rem;
    }
    
    .appointment-details p {
        font-size: 0.75rem;
    }
    
    .activity-list {
        gap: 0.75rem;
    }
    
    .activity-item {
        padding: 0.75rem;
    }
    
    .activity-icon {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .activity-title {
        font-size: 0.75rem;
    }
    
    .activity-description {
        font-size: 0.7rem;
    }
    
    .activity-time {
        font-size: 0.7rem;
    }
    
    .empty-state {
        padding: 2rem;
    }
    
    .empty-state h3 {
        font-size: 1.25rem;
    }
    
    .empty-state p {
        font-size: 0.875rem;
    }
    
    .btn-primary {
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .customer-main {
        padding: 0.75rem;
    }
    
    .customer-header {
        padding: 0.75rem;
    }
    
    .greeting {
        font-size: 1.5rem;
    }
    
    .subtitle {
        font-size: 0.85rem;
    }
    
    .stats-section .row {
        gap: 0.75rem;
    }
    
    .stat-card {
        padding: 1rem;
        min-height: 90px;
    }
    
    .stat-icon {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }
    
    .stat-content h3 {
        font-size: 1.125rem;
    }
    
    .stat-content p {
        font-size: 0.7rem;
    }
    
    .quick-actions h2 {
        font-size: 1.125rem;
        margin-bottom: 0.75rem;
    }
    
    .mobile-actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
        gap: 0.5rem;
    }
    
    .action-card {
        padding: 0.75rem;
        min-height: 85px;
    }
    
    .action-icon {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .action-title {
        font-size: 0.75rem;
    }
    
    .pets-section h2,
    .appointments-section h2,
    .activity-section h2 {
        font-size: 1.125rem;
        margin-bottom: 0.75rem;
    }
    
    .pets-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .pet-card {
        padding: 1rem;
    }
    
    .pet-avatar {
        width: 45px;
        height: 45px;
    }
    
    .pet-info h4 {
        font-size: 0.85rem;
    }
    
    .pet-info p {
        font-size: 0.7rem;
    }
    
    .appointments-list {
        gap: 0.75rem;
        padding: 1rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 1.75rem;
    }
    
    .stat-content h3 {
        font-size: 1.5rem;
    }
    
    .action-card {
        padding: 1.25rem;
        min-height: 260px;
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        font-size: 1.75rem;
    }
    
    .pet-card, .appointment-card {
        padding: 1rem;
    }
    
    .pet-avatar {
        width: 60px;
        height: 60px;
    }
}

/* Tablet Specific Optimizations */
@media (min-width: 768px) and (max-width: 1024px) {
    .desktop-actions {
        display: none;
    }
    
    .mobile-quick-actions {
        display: block;
    }
    
    .mobile-actions-container {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        padding: 1.75rem;
    }
    
    .mobile-actions-grid {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1.25rem;
    }
    
    .mobile-action-icon {
        font-size: 2.25rem;
    }
    
    .mobile-action-text {
        font-size: 0.8rem;
    }
}

/* Enhanced Accessibility and Focus States */
.action-card:focus-visible,
.btn-view:focus-visible,
.btn-primary:focus-visible,
.btn-add:focus-visible,
.btn-view-all:focus-visible,
.mobile-action-item:focus-visible {
    outline: 3px solid var(--primary-purple);
    outline-offset: 2px;
}

.stat-card:focus-visible,
.pet-card:focus-visible,
.appointment-card:focus-visible,
.activity-item:focus-visible {
    outline: 2px solid var(--primary-purple);
    outline-offset: 2px;
}

/* Enhanced Visual Hierarchy */
.customer-header {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(25px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.15);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
    box-shadow: 0 4px 20px rgba(147, 51, 234, 0.08);
}
/* Enhanced Loading States */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(167, 139, 250, 0.2), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

/* Enhanced Print Styles */
@media print {
    .customer-header,
    .quick-actions,
    .mobile-quick-actions {
        display: none !important;
    }
    
    .customer-main {
        padding: 0;
        max-width: none;
    }
    
    .stat-card,
    .action-card,
    .pet-card,
    .appointment-card,
    .activity-item {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}

/* Hide mobile Quick Actions on desktop by default */
.mobile-quick-actions {
    display: none;
}

/* Override for mobile and tablet */
@media (max-width: 1024px) {
    .mobile-quick-actions {
        display: block !important;
    }
    
    .desktop-actions {
        display: none !important;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <!-- Main Content -->
    <main class="customer-main">
        <!-- Stats Cards -->
        <section class="stats-section mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="stat-card pets">
                        <div class="stat-icon">🐕</div>
                        <div class="stat-content">
                            <h3>{{ $petCount }}</h3>
                            <p>My Pets</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card appointments">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <h3>{{ $upcomingCount }}</h3>
                            <p>Upcoming Appointments</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card health">
                        <div class="stat-icon">💊</div>
                        <div class="stat-content">
                            <h3>Active</h3>
                            <p>Treatments</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions mb-4">
            <h2 class="mb-3">Quick Actions</h2>
            <!-- Desktop Version -->
            <div class="desktop-actions">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.appointments.create') }}" class="action-card text-decoration-none">
                            <div class="action-icon">📅</div>
                            <h3>Book Appointment</h3>
                            <p>Schedule a visit for your pet</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.pets.create') }}" class="action-card text-decoration-none">
                            <div class="action-icon">🐾</div>
                            <h3>Add Pet</h3>
                            <p>Register a new pet</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.medical-records.index') }}" class="action-card text-decoration-none">
                            <div class="action-icon">📋</div>
                            <h3>Medical Records</h3>
                            <p>View pet health history</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.incidents.create') }}" class="action-card text-decoration-none">
                            <div class="action-icon">🚨</div>
                            <h3>Report Incident</h3>
                            <p>Report an accident or urgent concern</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.billing.orders') }}" class="action-card text-decoration-none">
                            <div class="action-icon">📋</div>
                            <h3>My Orders</h3>
                            <p>View order history and status</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.billing.index') }}" class="action-card text-decoration-none">
                            <div class="action-icon">💳</div>
                            <h3>Billing</h3>
                            <p>View invoices and payments</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.products.index') }}" class="action-card text-decoration-none">
                            <div class="action-icon">🛍️</div>
                            <h3>Shop</h3>
                            <p>Browse pet products</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('customer.cart.index') }}" class="action-card text-decoration-none">
                            <div class="action-icon cart-icon">🛒</div>
                            <h3>Cart</h3>
                            <p>View your shopping cart</p>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mobile Quick Actions -->
        <section class="mobile-quick-actions">
            <h2>Quick Actions</h2>
            <div class="mobile-actions-container">
                <div class="mobile-actions-grid">
                    <a href="{{ route('customer.appointments.create') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">📅</div>
                        <span class="mobile-action-text">Book Appointment</span>
                    </a>
                    <a href="{{ route('customer.pets.create') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">🐾</div>
                        <span class="mobile-action-text">Add Pet</span>
                    </a>
                    <a href="{{ route('customer.medical-records.index') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">📋</div>
                        <span class="mobile-action-text">Medical Records</span>
                    </a>
                    <a href="{{ route('customer.incidents.create') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">🚨</div>
                        <span class="mobile-action-text">Report Incident</span>
                    </a>
                    <a href="{{ route('customer.billing.orders') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">📋</div>
                        <span class="mobile-action-text">My Orders</span>
                    </a>
                    <a href="{{ route('customer.billing.index') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">💳</div>
                        <span class="mobile-action-text">Billing</span>
                    </a>
                    <a href="{{ route('customer.products.index') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">🛍️</div>
                        <span class="mobile-action-text">Shop</span>
                    </a>
                    <a href="{{ route('customer.cart.index') }}" class="mobile-action-item">
                        <div class="mobile-action-icon">🛒</div>
                        <span class="mobile-action-text">Cart</span>
                    </a>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <!-- Pets Section -->
            <!-- My Pets Section -->
            <section class="col-lg-6">
                <div class="pets-section">
                    <div class="section-header d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">My Pets</h2>
                        <a href="{{ route('customer.pets.create') }}" class="btn-add">+ Add New Pet</a>
                    </div>
                <div class="pets-list">
                    @if($pets->count() > 0)
                        @foreach($pets as $pet)
                            <div class="pet-card">
                                <div class="pet-info">
                                    <div class="pet-avatar">
                                        <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}">
                                    </div>
                                    <div class="pet-details">
                                        <h3>{{ $pet->name }}</h3>
                                        <p>{{ $pet->species }} • {{ $pet->breed ?? 'Mixed' }}</p>
                                        @if($pet->birth_date)
                                            <p class="age">{{ \Carbon\Carbon::parse($pet->birth_date)->age }} years old</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="pet-actions">
                                    <a href="#" class="btn-view">View Details</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">🐾</div>
                            <h3>No pets registered yet</h3>
                            <p>Add your first pet to get started</p>
                            <a href="{{ route('customer.pets.create') }}" class="btn-primary">Add Your First Pet</a>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Upcoming Appointments -->
            <section class="col-lg-6">
                <div class="appointments-section">
                    <div class="section-header d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Upcoming Appointments</h2>
                        <a href="{{ route('customer.appointments.index') }}" class="btn-view-all">View All</a>
                    </div>
                <div class="appointments-list">
                    @if($upcomingAppointments->count() > 0)
                        @foreach($upcomingAppointments as $appointment)
                            <div class="appointment-card">
                                <div class="appointment-date">
                                    <div class="date-day">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</div>
                                    <div class="date-month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</div>
                                </div>
                                <div class="appointment-details">
                                    <h4>{{ $appointment->type }}</h4>
                                    <p>{{ $appointment->pet->name }}</p>
                                    <p class="time">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}</p>
                                    @if($appointment->notes && $appointment->status === 'cancelled')
                                        <p style="font-size: 0.85rem; color: #991b1b; margin-top: 0.5rem; padding: 0.5rem; background: rgba(254, 202, 202, 0.3); border-radius: 0.5rem;">
                                            <strong>⚠️ Cancelled:</strong> {{ Str::limit($appointment->notes, 80) }}
                                        </p>
                                    @elseif($appointment->notes)
                                        <p style="font-size: 0.85rem; color: #6B7280; margin-top: 0.5rem;">
                                            <strong>📝:</strong> {{ Str::limit($appointment->notes, 80) }}
                                        </p>
                                    @endif
                                </div>
                                <div class="appointment-status">
                                    <span class="status-badge {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">📅</div>
                            <h3>No upcoming appointments</h3>
                            <p>Schedule your next visit</p>
                            <a href="{{ route('customer.appointments.create') }}" class="btn-primary">Book Appointment</a>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Recent Activity -->
        @if($recentAppointments->count() > 0)
        <section class="recent-activity mb-4">
            <h2 class="mb-3">Recent Visits</h2>
            <div class="activity-list">
                @foreach($recentAppointments as $appointment)
                    <div class="activity-item">
                        <div class="activity-icon">✓</div>
                        <div class="activity-content">
                            <h4>{{ $appointment->type }} - {{ $appointment->pet->name }}</h4>
                            <p>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                        </div>
                        <a href="#" class="btn-view">View Details</a>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    </main>
</div>
@endsection
