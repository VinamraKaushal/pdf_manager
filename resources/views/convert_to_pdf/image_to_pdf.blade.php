@include('layout.header')

@include('components.document_upload', [
  'title' => __('Upload Images to Convert to PDF'),
  'route' => route('convert.image.to.pdf'),
  'inputName' => 'images[]',
  'accept' => 'image/*',
  'sessionKey' => 'converted_images',
  'folder' => 'image',
  'label' => __('Choose Images'),
  'icon' => 'bi-file-earmark-image'
])

@include('layout.footer')
