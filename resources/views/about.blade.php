@include('layout.header')

<section class="bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="fw-bold text-primary">{{ __('About Us') }}</h1>
      <p class="text-muted lead">{{ __('Empowering your PDF workflows with simplicity and speed.') }}</p>
    </div>

    <div class="row align-items-center mb-5">
      <div class="col-md-6">
        <img src="{{ asset('images/about_us.png') }}" alt="About Us" class="img-fluid">
      </div>
      <div class="col-md-6">
        <h3 class="text-primary mb-3">{{ __('Who We Are') }}</h3>
        <p>{{ __('PDF Manager is a modern tool built to solve real-world PDF needs. Whether merging, compressing, or converting, we make it effortless and efficient.') }}</p>
        <p>{{ __('Our mission is to create accessible tools that save your time and protect your data.') }}</p>
      </div>
    </div>

    <div class="row text-center">
      <div class="col-md-3 mb-4">
        <div class="bg-white p-4 rounded shadow-sm h-100">
          <i class="bi bi-lightning-charge text-primary display-4"></i>
          <h5 class="mt-3">{{ __('Fast') }}</h5>
          <p class="text-muted small">{{ __('Lightning-speed PDF operations.') }}</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="bg-white p-4 rounded shadow-sm h-100">
          <i class="bi bi-shield-lock text-primary display-4"></i>
          <h5 class="mt-3">{{ __('Secure') }}</h5>
          <p class="text-muted small">{{ __('Your data is never stored or shared.') }}</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="bg-white p-4 rounded shadow-sm h-100">
          <i class="bi bi-box-arrow-in-down text-primary display-4"></i>
          <h5 class="mt-3">{{ __('No Install') }}</h5>
          <p class="text-muted small">{{ __('Completely browser-based. Nothing to install.') }}</p>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="bg-white p-4 rounded shadow-sm h-100">
          <i class="bi bi-stars text-primary display-4"></i>
          <h5 class="mt-3">{{ __('Easy to Use') }}</h5>
          <p class="text-muted small">{{ __('Simple UI for everyone.') }}</p>
        </div>
      </div>
    </div>
  </div>
</section>

@include('layout.footer')
