<div class="position-relative">

    {{-- Page Content (Blurred if guest) --}}
    <div class="{{ auth()->guest() ? 'blurred' : '' }}">
        @include('layout.header')

        @include('components.pdf_lock_unlock_upload', [
            'title' => __('Upload PDFs to Lock'),
            'route' => route('pdf.lock'),
            'inputName' => 'pdfs[]',
            'accept' => '.pdf',
            'sessionKey' => 'locked_pdfs',
            'folder' => 'locked_pdfs',
            'label' => __('Choose PDF Files to Lock'),
            'icon' => 'bi-lock-fill'
        ])

        @include('layout.footer')
    </div>

    {{-- Overlay for Guests --}}
    @guest
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
         style="background: rgba(255, 255, 255, 0.75); z-index: 1050;">
        <div class="text-center p-4 px-5 bg-white border border-2 rounded-4 shadow-lg">
            <div class="mb-3">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>
            </div>
            <h5 class="mb-2 fw-bold text-dark">Login Required</h5>
            <p class="mb-3 text-muted">Please log in to access the PDF Lock feature.</p>
            <button class="btn btn-primary px-4 py-2 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#authModal">
                Login Now
            </button>
        </div>
    </div>
    @endguest

</div>

@push('styles')
<style>
    .blurred {
        filter: blur(4px);
        pointer-events: none;
        user-select: none;
    }
</style>
@endpush
