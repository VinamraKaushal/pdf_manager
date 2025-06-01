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
