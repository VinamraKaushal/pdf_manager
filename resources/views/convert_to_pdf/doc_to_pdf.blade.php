@include('layout.header')

@include('components.document_upload', [
  'title' => __('Upload Word Documents to Convert to PDF'),
  'route' => route('convert.word.to.pdf'),
  'inputName' => 'documents[]',
  'accept' => '.doc,.docx,.odt,.txt',
  'sessionKey' => 'converted_docs',
  'folder' => 'word',
  'label' => __('Choose Word Documents'),
  'icon' => 'bi-file-earmark-word-fill'
])

@include('layout.footer')
