@include('layout.header')

@include('components.document_upload', [
  'title' => __('Convert PDF to PNG'),
  'route' => route('convert.pdf.to.png'),
  'inputName' => 'pdfs[]',
  'accept' => '.pdf',
  'sessionKey' => 'converted_pdf_to_png',
  'folder' => 'pdf_to_png',
  'label' => __('Choose PDF Files'),
  'icon' => 'bi-file-earmark-image'
])

@include('layout.footer')