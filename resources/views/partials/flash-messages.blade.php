@php($containerClass = $containerClass ?? '')
@php($showSuccess = $showSuccess ?? true)
@php($showError = $showError ?? true)
@php($showWarning = $showWarning ?? true)
@php($showInfo = $showInfo ?? true)

@if(($showSuccess && session('success')) || ($showError && session('error')) || ($showWarning && session('warning')) || ($showInfo && session('info')))
    <div class="{{ $containerClass }}">
        @if($showSuccess && session('success'))
            <div class="alert alert-success alert-dismissible fade show app-flash-alert" role="alert">
                <span class="app-flash-icon" aria-hidden="true">✓</span>
                <span class="app-flash-message">{{ session('success') }}</span>
                <button type="button" class="btn-close app-flash-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($showError && session('error'))
            <div class="alert alert-danger alert-dismissible fade show app-flash-alert" role="alert">
                <span class="app-flash-icon" aria-hidden="true">✕</span>
                <span class="app-flash-message">{{ session('error') }}</span>
                <button type="button" class="btn-close app-flash-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($showWarning && session('warning'))
            <div class="alert alert-warning alert-dismissible fade show app-flash-alert" role="alert">
                <span class="app-flash-icon" aria-hidden="true">!</span>
                <span class="app-flash-message">{{ session('warning') }}</span>
                <button type="button" class="btn-close app-flash-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($showInfo && session('info'))
            <div class="alert alert-info alert-dismissible fade show app-flash-alert" role="alert">
                <span class="app-flash-icon" aria-hidden="true">i</span>
                <span class="app-flash-message">{{ session('info') }}</span>
                <button type="button" class="btn-close app-flash-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
@endif
