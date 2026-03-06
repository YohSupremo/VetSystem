@push('styles')
<style>
/* Floating Action Button for QR Scan */
.fab-qr-scan {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a855f7, #ec4899);
    box-shadow: 0 8px 24px rgba(168, 85, 247, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 999;
    border: none;
    cursor: pointer;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.fab-qr-scan::before {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    padding: 3px;
    background: linear-gradient(135deg, rgba(255,255,255,0.4), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.fab-qr-scan:hover {
    transform: scale(1.1) translateY(-5px);
    box-shadow: 0 12px 32px rgba(168, 85, 247, 0.5);
    text-decoration: none;
    color: white;
}

.fab-qr-scan:hover::before {
    opacity: 1;
}

.fab-qr-scan:active {
    transform: scale(0.95);
}

/* Pulse animation */
@keyframes pulse-fab {
    0%, 100% {
        box-shadow: 0 8px 24px rgba(168, 85, 247, 0.4);
    }
    50% {
        box-shadow: 0 8px 32px rgba(168, 85, 247, 0.6);
    }
}

.fab-qr-scan {
    animation: pulse-fab 2s infinite;
}

/* Tooltip */
.fab-tooltip {
    position: absolute;
    bottom: 100%;
    right: 0;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
    margin-bottom: 10px;
    pointer-events: none;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.fab-qr-scan:hover .fab-tooltip {
    opacity: 1;
    transform: translateY(0);
}

/* Mobile responsiveness */
@media (max-width: 576px) {
    .fab-qr-scan {
        width: 60px;
        height: 60px;
        bottom: 20px;
        right: 20px;
        font-size: 1.5rem;
    }
}
</style>
@endpush

<!-- Floating Action Button for QR Scan -->
<a href="{{ route('customer.pets.scan') }}" class="fab-qr-scan" title="Scan Pet QR Code">
    <span class="fab-tooltip">Scan Pet QR</span>
    <i class="fas fa-qrcode"></i>
</a>
