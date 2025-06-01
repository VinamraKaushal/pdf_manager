@include('layout.header')

@include('components.document_upload', [
  'title' => __('Convert PDF to JPG'),
  'route' => route('convert.pdf.to.jpg'),
  'inputName' => 'pdfs[]',
  'accept' => '.pdf',
  'sessionKey' => 'converted_pdf_to_jpg',
  'folder' => 'pdf_to_jpg',
  'label' => __('Choose PDF Files'),
  'icon' => 'bi-file-earmark-image-fill'
])

@include('layout.footer')