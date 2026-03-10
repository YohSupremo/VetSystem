@extends('admin.dashboard')

@section('page-title', 'Cage Management')
@section('page-description', 'Manage boarding cages and assignments')

@section('content')
<style>
    /* Simplified Theme */
    :root {
        --primary: #FF7E7E;
        --bg: #F9FAFB;
        --card: #FFFFFF;
        --text: #1F2937;
        --text-light: #6B7280;
        --border: #E5E7EB;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
    }

    .container {
        background: var(--bg);
        min-height: 100vh;
        padding: 2rem;
    }

    /* Header */
    .header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
    }

    .btn-print {
        background: var(--card);
        border: 1px solid var(--border);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        color: var(--text);
        font-size: 0.875rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-print:hover {
        background: var(--bg);
    }

    /* Stats Grid */
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-light);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
    }

    .stat-card.available .stat-value { color: var(--success); }
    .stat-card.occupied .stat-value { color: var(--danger); }
    .stat-card.maintenance .stat-value { color: var(--warning); }

    /* Cages Grid */
    .cages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .cage-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1.25rem;
    }

    .cage-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .cage-code {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.25rem;
    }

    .cage-location {
        font-size: 0.875rem;
        color: var(--text-light);
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.available {
        background: #D1FAE5;
        color: #065F46;
    }

    .status-badge.occupied {
        background: #FEE2E2;
        color: #991B1B;
    }

    .status-badge.maintenance {
        background: #FEF3C7;
        color: #92400E;
    }

    .cage-size {
        font-size: 0.875rem;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .cage-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }

    .btn-qr, .btn-details {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .btn-qr {
        background: none;
        border: 1px solid var(--border);
        color: var(--text);
    }

    .btn-qr:hover {
        background: var(--bg);
    }

    .btn-details {
        background: var(--primary);
        color: white;
        border: none;
    }

    .btn-details:hover {
        opacity: 0.9;
    }

    .btn-release {
        background: #10B981;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .btn-release:hover {
        opacity: 0.9;
    }

    /* Pagination */
    .pagination {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-info {
        font-size: 0.875rem;
        color: var(--text-light);
    }

    .pagination-controls {
        display: flex;
        gap: 0.5rem;
    }

    .page-btn {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
        text-decoration: none;
        color: var(--text);
    }

    .page-btn:hover:not(.disabled) {
        background: var(--bg);
    }

    .page-btn.disabled {
        color: var(--text-light);
        cursor: not-allowed;
    }

    .page-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Modal */
    .modal {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        padding: 1rem;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: var(--card);
        border-radius: 8px;
        max-width: 400px;
        width: 100%;
    }

    .modal-header {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--text-light);
        cursor: pointer;
    }

    .modal-body {
        padding: 1.5rem;
        text-align: center;
    }

    .qr-code-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .qr-subtitle {
        font-size: 0.875rem;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .qr-image {
        margin: 1rem 0;
    }

    .qr-image img {
        width: 200px;
        height: 200px;
    }

    .qr-url {
        font-size: 0.75rem;
        color: var(--text-light);
        word-break: break-all;
        background: var(--bg);
        padding: 0.75rem;
        border-radius: 6px;
    }

    .modal-footer {
        padding: 1rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn-modal {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
    }

    .btn-cancel {
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--text);
    }

    .btn-print-qr {
        background: var(--primary);
        color: white;
        border: none;
    }

    /* Empty State */
    .empty {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 3rem;
        text-align: center;
    }

    .empty i {
        font-size: 3rem;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .empty h3 {
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
    }

    .empty p {
        color: var(--text-light);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }

        .stats {
            grid-template-columns: 1fr;
        }

        .cages-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container">

    @if(session('warning'))
        <div class="alert alert-warning mb-3">{{ session('warning') }}</div>
    @endif

    <!-- Header -->
    <div class="header">
        <h1 class="title">Cage Management</h1>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i>
            Print List
        </button>
    </div>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card available">
            <div class="stat-label">Available</div>
            <div class="stat-value">{{ $cages->where('status', 'available')->count() }}</div>
        </div>
        
        <div class="stat-card occupied">
            <div class="stat-label">Occupied</div>
            <div class="stat-value">{{ $cages->where('status', 'occupied')->count() }}</div>
        </div>
        
        <div class="stat-card maintenance">
            <div class="stat-label">Maintenance</div>
            <div class="stat-value">{{ $cages->where('status', 'maintenance')->count() }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">Total Cages</div>
            <div class="stat-value">{{ $cages->total() }}</div>
        </div>
    </div>

    <!-- Cages Grid -->
    <div class="cages-grid">
        @forelse($cages as $cage)
            <div class="cage-card">
                <div class="cage-header">
                    <div>
                        <h3 class="cage-code">{{ $cage->cage_code }}</h3>
                        <p class="cage-location">{{ $cage->location }}</p>
                    </div>
                    <span class="status-badge {{ $cage->status }}">
                        {{ ucfirst($cage->status) }}
                    </span>
                </div>

                <div class="cage-size">
                    Size: {{ ucfirst(str_replace('_', ' ', $cage->size)) }}
                </div>

                <div class="cage-actions">
                            <button onclick="showQr('{{ $cage->cage_code }}', '{{ $scanBaseUrl . route('admin.cages.scan', ['code' => $cage->cage_code], false) }}')" 
                            class="btn-qr">
                        <i class="fas fa-qrcode"></i>
                        QR Code
                    </button>
                    @if($cage->status === 'occupied')
                        <form method="POST" action="{{ route('admin.cages.release', $cage->id) }}" onsubmit="return confirm('Release pet from this cage?');" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-release">
                                <i class="fas fa-sign-out-alt"></i>
                                Release
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.cages.show', $cage->id) }}" class="btn-details">
                        Details
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="empty">
                <i class="fas fa-box-open"></i>
                <h3>No Cages Found</h3>
                <p>Add cages to get started</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($cages->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing {{ $cages->firstItem() }} to {{ $cages->lastItem() }} of {{ $cages->total() }}
            </div>
            
            <div class="pagination-controls">
                @if ($cages->onFirstPage())
                    <span class="page-btn disabled">Previous</span>
                @else
                    <a href="{{ $cages->previousPageUrl() }}" class="page-btn">Previous</a>
                @endif

                @foreach ($cages->getUrlRange(1, $cages->lastPage()) as $page => $url)
                    @if ($page == $cages->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($cages->hasMorePages())
                    <a href="{{ $cages->nextPageUrl() }}" class="page-btn">Next</a>
                @else
                    <span class="page-btn disabled">Next</span>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- QR Modal -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">QR Code</h3>
            <button class="modal-close" onclick="closeQr()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <h2 class="qr-code-title" id="cageCode"></h2>
            <p class="qr-subtitle">Scan for details</p>
            
            <div class="qr-image">
                <img id="qrImage" src="" alt="QR Code">
            </div>
            
            <div class="qr-url" id="qrUrl"></div>
        </div>
        
        <div class="modal-footer">
            <button class="btn-modal btn-cancel" onclick="closeQr()">
                Cancel
            </button>
            <button class="btn-modal btn-print-qr" onclick="printQr()">
                <i class="fas fa-print"></i>
                Print
            </button>
        </div>
    </div>
</div>

<script>
    function showQr(code, url) {
        document.getElementById('cageCode').innerText = code;
        document.getElementById('qrUrl').innerText = url;
        document.getElementById('qrImage').src = `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${encodeURIComponent(url)}`;
        document.getElementById('qrModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeQr() {
        document.getElementById('qrModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    function printQr() {
        const code = document.getElementById('cageCode').innerText;
        const qrSrc = document.getElementById('qrImage').src;
        const url = document.getElementById('qrUrl').innerText;
        
        const win = window.open('', '', 'width=600,height=700');
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print QR Code</title>
                <style>
                    body { 
                        font-family: system-ui, -apple-system, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 20px;
                    }
                    .card { 
                        text-align: center;
                        border: 2px dashed #ddd;
                        padding: 40px;
                        border-radius: 8px;
                        max-width: 400px;
                    }
                    h2 { 
                        font-size: 2rem;
                        margin-bottom: 10px;
                    }
                    p { 
                        color: #666;
                        margin-bottom: 20px;
                    }
                    img { 
                        width: 200px;
                        height: 200px;
                        margin: 20px 0;
                    }
                    .url { 
                        font-size: 0.75rem;
                        color: #999;
                        word-break: break-all;
                        background: #f5f5f5;
                        padding: 10px;
                        border-radius: 4px;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="card">
                    <h2>${code}</h2>
                    <p>Scan for Details</p>
                    <img src="${qrSrc}" alt="QR Code" />
                    <div class="url">${url}</div>
                </div>
            </body>
            </html>
        `);
        
        win.document.close();
        setTimeout(() => {
            win.print();
            win.close();
        }, 500);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeQr();
    });
</script>
@endsection