@include('layout.header')
<style>
  .article-box {
    display: block;
    border-radius: 0.5rem;
    transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
    position: relative;
    height: 175px;
    overflow: hidden;
  }

  .article-box:hover {
    background-color: #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    transform: scale(1.05);
    text-decoration: none;
    z-index: 10;
  }

  .article-box .truncate-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-word;
    line-height: 1.6;
    max-height: 5.2em;
  }
</style>
<section class="bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="fw-bold text-primary">{{ __('Help Center') }}</h1>
      <p class="text-secondary fs-5">{{ __('Find quick answers or contact us for personalized support.') }}</p>
    </div>

    <!-- Tabbed Article Section -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="helpTabs" role="tablist">
      @php
        $categories = ['general' => 'General', 'conversion' => 'Conversion Tools', 'pdf_utilities' => 'PDF Utilities'];
      @endphp
      @foreach ($categories as $key => $label)
        <li class="nav-item" role="presentation">
          <button class="nav-link @if($loop->first) active @endif" id="{{ $key }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $key }}" type="button" role="tab">
            {{ $label }}
          </button>
        </li>
      @endforeach
    </ul>

    <div class="tab-content" id="helpTabsContent">
      @php
        $articlesByCategory = [
          'general' => [
            [
              'title' => 'How to get started with PDF Manager?',
              'desc' => 'A quick guide to begin using PDF Manager efficiently.'
            ],
          ],
          'conversion' => [
            [
              'title' => 'How to convert Word, Excel, and PowerPoint to PDF?',
              'desc' => 'Go to the "Convert to PDF" section, upload your .docx, .xlsx, or .pptx file, and click "Convert". The PDF will be generated and available for download in seconds.'
            ],
            [
              'title' => 'Can I convert images to PDF?',
              'desc' => 'Yes. Use the image-to-PDF converter, upload JPG, PNG, or BMP files, arrange their order if needed, and click "Convert" to generate a single PDF file.'
            ],
            [
              'title' => 'How to convert PDF to other formats?',
              'desc' => 'Visit the "Convert from PDF" tab, upload your PDF, and choose whether to convert it to Word, JPG, or PNG. Each page of the PDF will be processed accordingly.'
            ],
          ],
          'pdf_utilities' => [
            [
              'title' => 'How to merge multiple PDFs?',
              'desc' => 'Navigate to the "Merge PDFs" tool, drag and drop multiple PDF files, rearrange their order if needed, and click "Merge". A single combined PDF will be created.'
            ],
            [
              'title' => 'How to split a PDF?',
              'desc' => 'Use the "Split PDF" feature by uploading your PDF, selecting the page range or individual pages to extract, and clicking "Split" to generate a new file.'
            ],
            [
              'title' => 'Is it possible to lock or encrypt my PDF?',
              'desc' => 'Yes. Go to the "Lock PDF" tool, upload your file, set a password, and click "Encrypt". The file will be password-protected and safe to share.'
            ],
          ],
        ];
      @endphp

      @foreach ($articlesByCategory as $key => $articles)
        <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ $key }}" role="tabpanel">
          <div class="row gy-4">
            @foreach ($articles as $article)
              <div class="col-md-4">
                <a href="#" class="text-decoration-none article-box" data-bs-toggle="modal" data-bs-target="#articleModal"
                   data-title="{{ $article['title'] }}" data-desc="{{ $article['desc'] }}">
                  <div class="border rounded-3 p-4 h-100 shadow-sm hover-shadow transition">
                    <h5 class="text-primary fw-bold">{{ $article['title'] }}</h5>
                    <p class="text-muted small mb-0 truncate-text">{{ $article['desc'] }}</p>
                  </div>
                </a>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

    <!-- Contact Email -->
    <div class="text-center pt-5 mt-5 border-top" style="border-color: #dee2e6 !important;">
      <h3 class="fw-semibold text-primary mb-3">{{ __('Still Need Help?') }}</h3>
      <p class="mb-1 text-secondary fs-5">{{ __('Contact our support team via email and we’ll get back to you as soon as possible.') }}</p>
      <a href="mailto:support@pdfmanager.com" class="text-primary fw-semibold fs-5 text-decoration-none">
        <i class="bi bi-envelope-fill me-2"></i>{{ __('support@pdfmanager.com') }}
      </a>
    </div>
  </div>
</section>

<!-- Modal -->
<div class="modal fade" id="articleModal" tabindex="-1" aria-labelledby="articleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-white text-primary rounded-top-4">
        <div class="d-flex align-items-center">
          <i class="bi bi-info-circle-fill fs-3 me-2"></i>
          <h5 class="modal-title fw-semibold mb-0" id="articleModalLabel">{{ __('Article Title') }}</h5>
        </div>
      </div>
      <div class="modal-body p-4">
        <p class="text-secondary fs-5" id="articleModalDesc">{{ __('Article description will appear here.') }}</p>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
          {{ __('Close') }}
        </button>
      </div>
    </div>
  </div>
</div>

@include('layout.footer')

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modalTitle = document.getElementById('articleModalLabel');
    const modalDesc = document.getElementById('articleModalDesc');

    document.querySelectorAll('.article-box').forEach(function (box) {
      box.addEventListener('click', function () {
        modalTitle.textContent = this.getAttribute('data-title');
        modalDesc.textContent = this.getAttribute('data-desc');
      });
    });
  });
</script>
