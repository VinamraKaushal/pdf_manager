<div class="position-relative">

    {{-- Page Content (Blurred if guest) --}}
    <div class="{{ auth()->guest() ? 'blurred' : '' }}">
        @include('layout.header')

        @include('components.document_upload', [
          'title' => __('Upload Excel Files to Convert to PDF'),
          'route' => route('convert.excel.to.pdf'),
          'inputName' => 'excels[]',
          'accept' => '.xls,.xlsx,.ods',
          'sessionKey' => 'converted_excels',
          'folder' => 'excel',
          'label' => __('Choose Excel Files'),
          'icon' => 'bi-file-earmark-spreadsheet-fill'
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
