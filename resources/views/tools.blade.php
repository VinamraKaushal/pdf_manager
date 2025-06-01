@include('layout.header')
<style>
  .tool-box {
    border-bottom: 2px solid transparent;
    transition: border-color 0.3s ease, background-color 0.3s ease;
    user-select: none;
    border-radius: 4px;
  }
  .tool-box:hover, .tool-box:focus {
    border-bottom-color: #0d6efd;
    background-color: #f8f9fa;
    color: #0d6efd !important;
  }
  .icon-word { color: #2B579A; }
  .icon-excel { color: #217346; }
  .icon-ppt { color: #D24726; }
  .icon-image { color: #6F42C1; }
  .icon-pdf { color: #E02F2F; }
  .icon-merge { color: #0D6EFD; }
  .icon-split { color: #6C757D; }
  .icon-compress { color: #FD7E14; }
  .icon-lock { color: #198754; }
  .icon-unlock { color: #FFC107; }
</style>

<section class="bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="fw-bold text-primary">{{ __('Tools') }}</h1>
      <p class="text-secondary fs-5">{{ __('Effortlessly transform your documents in just a few clicks.') }}</p>
    </div>
    <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 g-2 justify-content-center">
      @php
        $tools = [
          ['label' => 'Word to PDF',       'icon' => 'bi-file-earmark-word-fill icon-word', 'url' => url('/convert/word-to-pdf')],
          ['label' => 'Excel to PDF',      'icon' => 'bi-file-earmark-spreadsheet-fill icon-excel', 'url' => url('/convert/excel-to-pdf')],
          ['label' => 'PowerPoint to PDF', 'icon' => 'bi-file-earmark-ppt-fill icon-ppt', 'url' => url('/convert/ppt-to-pdf')],
          ['label' => 'Image to PDF',      'icon' => 'bi-file-earmark-image-fill icon-image', 'url' => url('/convert/image-to-pdf')],
          ['label' => 'PDF to Word',       'icon' => 'bi-file-earmark-word icon-pdf', 'url' => url('/convert/pdf-to-word')],
          ['label' => 'PDF to Excel',      'icon' => 'bi-file-earmark-spreadsheet icon-pdf', 'url' => url('/convert/pdf-to-excel')],
          ['label' => 'PDF to JPG',        'icon' => 'bi-file-earmark-image icon-pdf', 'url' => url('/convert/pdf-to-jpg')],
          ['label' => 'PDF to PNG',        'icon' => 'bi-file-earmark-image icon-pdf', 'url' => url('/convert/pdf-to-png')],
          ['label' => 'Merge PDFs',        'icon' => 'bi-files icon-merge', 'url' => url('/pdf/merge')],
          ['label' => 'Split PDFs',        'icon' => 'bi-scissors icon-split', 'url' => url('/pdf/split')],
          ['label' => 'Lock PDF',          'icon' => 'bi-lock-fill icon-lock', 'url' => url('/security/lock')],
          // ['label' => 'Unlock PDF',        'icon' => 'bi-unlock-fill icon-unlock', 'url' => url('/security/unlock')],
        ];
      @endphp
  
      @foreach($tools as $tool)
        <div class="col mb-2">
          <a href="{{ $tool['url'] }}" class="text-decoration-none d-block text-center tool-box py-2 px-1">
            <i class="bi {{ $tool['icon'] }} fs-4 mb-1"></i>
            <div class="small fw-medium text-dark">{{ $tool['label'] }}</div>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>

@include('layout.footer')
