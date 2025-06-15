<!-- Authentication Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
      <div class="modal-header bg-gradient text-white bg-primary py-3 px-4">
        <h5 class="modal-title fw-bold" id="authModalLabel">{{ __('Login to PDF Manager') }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 py-4 bg-light">
        <form id="authForm">
          <div id="nameField" class="mb-3 d-none">
            <label for="name" class="form-label fw-medium">{{ __('Name') }}</label>
            <input type="text" class="form-control rounded-3" id="name" name="name">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label fw-medium">{{ __('Email') }}</label>
            <input type="email" class="form-control rounded-3" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label fw-medium">{{ __('Password') }}</label>
            <input type="password" class="form-control rounded-3" id="password" name="password" required>
          </div>

          <!-- OTP Section -->
          <div class="mb-3 d-none" id="otpSection">
            <label for="otp" class="form-label fw-medium">{{ __('Enter OTP') }}</label>
            <input type="text" class="form-control rounded-3" id="otp" name="otp" maxlength="6">
            <div class="text-end mt-1">
              <button type="button" class="btn btn-sm btn-link text-decoration-none" id="resendOtp" disabled>{{ __('Resend OTP') }}</button>
            </div>
          </div>

          <div class="d-grid gap-2 mt-3">
            <button type="submit" class="btn btn-success rounded-pill fw-semibold py-2">{{ __('Submit') }}</button>
          </div>

          <div class="text-center mt-3" id="toggleLinkContainer">
            <small class="text-muted">{{ __('Not a user?') }}</small>
            <a href="#" class="text-primary text-decoration-underline fw-medium" id="toggleRegister">{{ __('Register here') }}</a>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const toggleRegister = document.getElementById('toggleRegister');
  const nameField = document.getElementById('nameField');
  const authModalLabel = document.getElementById('authModalLabel');
  const submitBtn = document.querySelector('#authForm button[type="submit"]');
  const otpSection = document.getElementById('otpSection');
  const authForm = document.getElementById('authForm');
  const resendBtn = document.getElementById('resendOtp');
  const toggleLinkContainer = document.getElementById('toggleLinkContainer');

  let isRegistering = false;

  toggleRegister.addEventListener('click', function (e) {
    e.preventDefault();
    nameField.classList.remove('d-none');
    authModalLabel.textContent = 'Register to PDF Manager';
    submitBtn.textContent = 'Register';
    isRegistering = true;

    toggleLinkContainer.innerHTML = `
      <small class="text-muted">{{ __('Already have an account?') }}</small>
      <a href="#" class="text-primary text-decoration-underline fw-medium" id="toggleLogin">{{ __('Login here') }}</a>
    `;

    document.getElementById('toggleLogin').addEventListener('click', function (e) {
      e.preventDefault();
      nameField.classList.add('d-none');
      authModalLabel.textContent = 'Login to PDF Manager';
      submitBtn.textContent = 'Submit';
      isRegistering = false;

      toggleLinkContainer.innerHTML = `
        <small class="text-muted">{{ __('Not a user?') }}</small>
        <a href="#" class="text-primary text-decoration-underline fw-medium" id="toggleRegister">{{ __('Register here') }}</a>
      `;
      document.getElementById('toggleRegister').addEventListener('click', arguments.callee); // Rebind register toggle
    });
  });

  authModal.addEventListener('hidden.bs.modal', () => {
    nameField.classList.add('d-none');
    otpSection.classList.add('d-none');
    authModalLabel.textContent = 'Login to PDF Manager';
    submitBtn.textContent = 'Submit';
    toggleRegister.closest('div').classList.remove('d-none');
    authForm.reset();
    isRegistering = false;

    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
  });

  authForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    const name = document.getElementById('name')?.value || '';
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const otp = document.getElementById('otp').value;
    const isOtpVisible = !otpSection.classList.contains('d-none');

    // Determine route
    const route = isOtpVisible
      ? (isRegistering ? '{{ route('auth.verify-otp') }}' : '{{ route('auth.verify-login-otp') }}')
      : (isRegistering ? '{{ route('auth.register') }}' : '{{ route('auth.initiate') }}');

    const res = await fetch(route, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ name, email, password, otp })
    });

    const data = await res.json();

    if (res.ok) {
      if (!isOtpVisible && data.otp_required) {
        otpSection.classList.remove('d-none');
        startOtpCooldown(); // Enable resend cooldown
      } else {
        location.reload();
      }
    } else {
      if (data.errors) {
        for (const field in data.errors) {
          const input = document.getElementById(field);
          if (input) {
            input.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.classList.add('invalid-feedback');
            feedback.innerText = data.errors[field][0];
            input.parentNode.appendChild(feedback);
          }
        }
      } else if (data.error) {
        alert(data.error); // fallback error
      }
    }
  });

  // Resend OTP with 2-minute cooldown
  resendBtn.addEventListener('click', async function () {
    const email = document.getElementById('email').value;
    if (!email) return alert("Email required to resend OTP.");

    resendBtn.disabled = true;
    resendBtn.innerText = 'Resending...';

    const res = await fetch('{{ route('auth.resend-otp') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ email })
    });

    if (res.ok) {
      startOtpCooldown();
    } else {
      const data = await res.json();
      alert(data.error || 'Failed to resend OTP.');
      resendBtn.disabled = false;
      resendBtn.innerText = 'Resend OTP';
    }
  });

  function startOtpCooldown() {
    let timeLeft = 120;
    resendBtn.disabled = true;
    const interval = setInterval(() => {
      resendBtn.innerText = `Resend OTP in ${timeLeft--}s`;
      if (timeLeft < 0) {
        clearInterval(interval);
        resendBtn.innerText = 'Resend OTP';
        resendBtn.disabled = false;
      }
    }, 1000);
  }
</script>
