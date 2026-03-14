@extends('layout.base')

@section('title', $pet->name . ' QR Code - PawCare')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-print me-1"></i> Print QR
                        </button>
                    </div>

                    <h2 class="h4 fw-bold mb-1">{{ $pet->name }} Medical Record QR</h2>
                    <p class="text-muted mb-4">Anyone can view this QR page. Scanning opens the linked medical-record route.</p>

                    <div class="bg-white border rounded-3 p-3 d-inline-block mb-3">
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=360x360&margin=10&ecc=H&data={{ urlencode($scanUrl) }}"
                            alt="{{ $pet->name }} QR Code"
                            width="280"
                            height="280"
                            style="max-width: 100%; height: auto;"
                        >
                    </div>

                    <div class="small text-muted mb-2">QR Destination</div>
                    <div class="small text-break" style="font-family: Consolas, monospace;">{{ $scanUrl }}</div>

                    <div class="mt-3">
                        <a href="{{ $scanUrl }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-qrcode me-1"></i> Simulate Scan
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="row g-2 text-start small">
                        <div class="col-sm-6"><strong>Pet:</strong> {{ $pet->name }}</div>
                        <div class="col-sm-6"><strong>Species:</strong> {{ $pet->species }}</div>
                        <div class="col-sm-6"><strong>Breed:</strong> {{ $pet->breed ?: 'N/A' }}</div>
                        <div class="col-sm-6"><strong>Registration #:</strong> {{ $pet->registration_number ?: 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
