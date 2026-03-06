@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', $pet->name . ' - Scan History')

@section('content')
@include('layout.customer-navbar')

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3 mb-4">
                @if($pet->photo_path)
                    <img src="{{ asset('storage/' . $pet->photo_path) }}" alt="{{ $pet->name }}" 
                        class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-paw fa-2x text-muted"></i>
                    </div>
                @endif
                <div>
                    <h2 class="mb-0">{{ $pet->name }}</h2>
                    <p class="text-muted mb-0">{{ ucfirst($pet->species) }} • {{ $pet->breed ?? 'N/A' }}</p>
                </div>
            </div>

            <a href="{{ route('customer.pets.scan') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Pets
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-history"></i> Scan History
            </h4>

            @if($logs->isEmpty())
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> No scans recorded yet for this pet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Scanned By</th>
                                <th>Location</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->scan_timestamp->format('M d, Y') }}</strong><br>
                                        <small class="text-muted">{{ $log->scan_timestamp->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        {{ $log->scannedBy->name ?? 'Unknown' }}<br>
                                        <small class="text-muted">{{ $log->scannedBy->role ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($log->location)
                                            <i class="fas fa-map-marker-alt"></i> {{ $log->location }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->notes)
                                            <small>{{ $log->notes }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    {{ $logs->links() }}
                </nav>
            @endif
        </div>
    </div>
</div>
@endsection
