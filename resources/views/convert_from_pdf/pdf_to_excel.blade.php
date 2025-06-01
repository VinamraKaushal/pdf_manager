@include('layout.header')

@include('components.document_upload', [
  'title' => __('Convert PDF to Excel'),
  'route' => route('convert.pdf.to.excel'),
  'inputName' => 'pdfs[]',
  'accept' => '.pdf',
  'sessionKey' => 'converted_pdf_to_excel',
  'folder' => 'pdf_to_excel',
  'label' => __('Choose PDF Files'),
  'icon' => 'bi-file-earmark-excel-fill'
])

@include('layout.footer')