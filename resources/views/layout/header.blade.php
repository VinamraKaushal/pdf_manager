<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('PDF Manager') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <style>
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background-color: #f8f9fa;
    }
    .navbar-brand {
      color: #0d6efd;
    }
    main {
      flex: 1;
    }
    .navbar .nav-link,
    .navbar .btn {
      padding-top: 0.5rem;
      padding-bottom: 0.5rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('home') }}">{{ __('PDF Manager') }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold text-primary' : '' }}" href="{{ route('home') }}">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('tools') ? 'active fw-bold text-primary' : '' }}" href="{{ route('tools') }}">Tools</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('about') ? 'active fw-bold text-primary' : '' }}" href="{{ route('about') }}">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('help_center') ? 'active fw-bold text-primary' : '' }}" href="{{ route('help_center') }}">Help Center</a>
        </li>

        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1" style="font-size: 1.4rem;"></i>
              <span>{{ Str::limit(auth()->user()->name, 12) }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li class="dropdown-header">Hi, {{ auth()->user()->name }}</li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                  </button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <li class="nav-item">
            <button class="btn btn-success ms-3" data-bs-toggle="modal" data-bs-target="#authModal">
              Login
            </button>
          </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<main class="py-5">
