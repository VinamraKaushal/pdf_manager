<style>
  body {
    background-color: #f8f9fa;
  }

  .upload-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 0 3rem;
  }

  .upload-card {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    border: none;
  }

  .custom-header {
    background: linear-gradient(to right, #0d6efd, #0b5ed7);
    padding: 1.25rem 2rem;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
    box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
  }

  .custom-header .icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.15);
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.50rem;
    margin-right: 1rem;
  }

  .custom-header h1 {
    font-weight: 600;
    font-size: 1.25rem;
    margin: 0;
    color: #fff;
  }

  .card-body {
    padding: 2.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .pdf-queue-wrapper {
    flex-grow: 1;
    overflow-y: auto;
    border-top: 1px solid #dee2e6;
    padding-top: 1.5rem;
  }

  .table-responsive {
    max-height: 400px;
    overflow-y: auto;
  }
</style>
<div class="upload-wrapper">
  <div class="card upload-card shadow-sm">
    <div class="card-header custom-header">
      <div class="icon-wrapper">
        <i class="bi {{ $icon ?? 'bi-upload' }} fs-4 text-white"></i>
      </div>
      <h1>{{ $title }}</h1>
    </div>

    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ $route }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="mb-4">
          <label for="document" class="form-label text-primary fw-semibold fs-6">{{ $label }}</label>
          <input type="file" class="form-control form-control-lg" id="document" name="{{ $inputName }}" required accept="{{ $accept }}" multiple>
        </div>
        <button type="submit" class="btn btn-lg btn-primary">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i> {{ __('Convert to PDF') }}
        </button>
      </form>

      <div class="pdf-queue-wrapper mt-4">
        <h5 class="text-primary mb-4"><i class="bi bi-clock-history me-2"></i>{{ __('Converted PDF Queue') }}</h5>
        @if(session($sessionKey) && count(session($sessionKey)))
          <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>{{ __('Original Name') }}</th>
                  <th class="text-end">{{ __('Download') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach(session($sessionKey) as $pdf)
                  <tr>
                    <td><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>{{ $pdf['original_name'] }}</td>
                    <td class="text-end">
                      <a href="{{ asset('storage/converted_pdfs/' . $folder . '/' . $pdf['file_name']) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="bi bi-download"></i> {{ __('Download') }}
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted">{{ __('No PDFs in queue.') }}</p>
        @endif
      </div>
    </div>
  </div>
</div>
