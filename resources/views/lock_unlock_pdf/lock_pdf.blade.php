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
