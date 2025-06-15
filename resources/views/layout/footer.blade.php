</main>

<footer class="bg-white border-top py-4 mt-auto text-center text-muted">
  <div class="container">
    &copy; {{ date('Y') }} {{ __('PDF Manager') }}. {{ __('All rights reserved.') }}
  </div>
</footer>

@include('layout.login_register')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    initGuestCredit();
  });

 function initGuestCredit() {
    fetch('{{ route('guest-credit.init') }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => {
      // handle success
    })
    .catch(err => {
      console.error('Init error:', err);
    });
  }

  function deductCredit() {
    fetch('{{ route('guest-credit.deduct') }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.credits !== undefined) {
        // handle credit update
      } else {
        alert(data.error || 'Unknown error');
      }
    })
    .catch(err => {
      console.error('Deduct error:', err);
    });
  }
</script>

</body>
</html>
