@include('layout.header')

@include('components.document_upload', [
  'title' => __('Upload PowerPoint Files to Convert to PDF'),
  'route' => route('convert.ppt.to.pdf'),
  'inputName' => 'presentations[]',
  'accept' => '.pptx',
  'sessionKey' => 'converted_ppts',
  'folder' => 'ppt',
  'label' => __('Choose PowerPoint Files'),
  'icon' => 'bi-file-earmark-slides-fill'
])

@include('layout.footer')
