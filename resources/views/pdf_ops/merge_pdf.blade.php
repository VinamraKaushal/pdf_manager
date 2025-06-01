@include('layout.header')

@include('components.document_upload', [
  'title' => __('Upload PDF Files to Merge'),
  'route' => route('pdf.merge'),
  'inputName' => 'pdfs[]',
  'accept' => '.pdf',
  'sessionKey' => 'merged_pdfs',
  'folder' => 'merged',
  'label' => __('Choose PDF Files to Merge'),
  'icon' => 'bi-file-earmark-pdf-fill'
])

@include('layout.footer')
