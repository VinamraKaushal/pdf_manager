@include('layout.header')

@include('components.pdf_lock_unlock_upload', [
  'title' => __('Upload PDFs to Unlock'),
  'route' => route('pdf.unlock'),
  'inputName' => 'pdfs[]',
  'accept' => '.pdf',
  'sessionKey' => 'unlocked_pdfs',
  'folder' => 'unlocked_pdfs',
  'label' => __('Choose PDF Files to Unlock'),
  'icon' => 'bi-unlock-fill'
])

@include('layout.footer')
