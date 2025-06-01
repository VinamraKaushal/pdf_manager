@include('layout.header')

@include('components.document_upload', [
  'title' => __('Upload PDF File to Split'),
  'route' => route('pdf.split'),
  'inputName' => 'pdf',
  'accept' => '.pdf',
  'sessionKey' => 'split_pdfs',
  'folder' => 'split',
  'label' => __('Choose a PDF File to Split'),
  'icon' => 'bi-file-earmark-pdf-fill'
])

@include('layout.footer')
