@include('layout.header')

<div class="container text-center">
  <h1 class="display-4 fw-bold text-primary">{{ __('Welcome to PDF Manager') }}</h1>
  <p class="lead text-secondary">{{ __('Your all-in-one online tool to effortlessly manage and transform PDF files.') }}</p>

  <div class="mt-4">
    <a href="/tools" class="btn btn-success btn-lg">{{ __('Start Manipulating PDFs') }}</a>
    {{-- <a href="/tools" class="btn btn-outline-primary btn-lg ms-3">{{ __('Explore Features') }}</a> --}}
  </div>

  <section id="features" class="row mt-5">
    <div class="col-md-4 mb-4">
      <a href="{{ url('/pdf/merge') }}" class="text-decoration-none text-reset">
        <div class="card shadow h-100 hover-shadow-sm">
          <div class="card-body">
            <h5 class="card-title text-primary">{{ __('Merge PDFs') }}</h5>
            <p class="card-text text-muted">{{ __('Combine multiple PDF files into one effortlessly.') }}</p>
          </div>
        </div>
      </a>
    </div>

    <div class="col-md-4 mb-4">
      <a href="{{ url('/pdf/split') }}" class="text-decoration-none text-reset">
        <div class="card shadow h-100">
          <div class="card-body">
            <h5 class="card-title text-primary">{{ __('Split PDFs') }}</h5>
            <p class="card-text text-muted">{{ __('Extract selected pages or split PDFs into smaller files.') }}</p>
          </div>
        </div>
      </a>
    </div>

    <div class="col-md-4 mb-4">
      <a href="{{ url('/security/lock') }}" class="text-decoration-none text-reset">
        <div class="card shadow h-100">
          <div class="card-body">
            <h5 class="card-title text-primary">{{ __('Lock PDFs') }}</h5>
            <p class="card-text text-muted">{{ __('Add password protection to your PDF files.') }}</p>
          </div>
        </div>
      </a>
    </div>
  </section>
</div>

@include('layout.footer')
