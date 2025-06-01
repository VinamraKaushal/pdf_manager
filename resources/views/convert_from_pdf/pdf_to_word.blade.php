@include('layout.header')

@include('components.document_upload', [
  'title' => __('Convert PDF to Word'),
  'route' => route('convert.pdf.to.word'),
  'inputName' => 'pdfs[]',
  'accept' => '.pdf',
  'sessionKey' => 'converted_pdf_to_word',
  'folder' => 'pdf_to_word',
  'label' => __('Choose PDF Files'),
  'icon' => 'bi-file-earmark-word-fill'
])

@include('layout.footer')