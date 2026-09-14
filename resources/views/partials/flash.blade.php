{{-- Flash Notification Toast --}}
@if (session('success') || session('error') || session('warning') || session('info'))
<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">

    @if (session('success'))
    <div class="toast align-items-center text-white bg-success border-0 mb-2 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="fas fa-check-circle me-1"></i>
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    @if (session('error'))
    <div class="toast align-items-center text-white bg-danger border-0 mb-2 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="fas fa-times-circle me-1"></i>
                {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    @if (session('warning'))
    <div class="toast align-items-center text-white bg-warning border-0 mb-2 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle me-1"></i>
                {{ session('warning') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    @if (session('info'))
    <div class="toast align-items-center text-white bg-info border-0 mb-2 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="fas fa-info-circle me-1"></i>
                {{ session('info') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-initialize semua toast yang ada
        var toastEls = document.querySelectorAll('.toast');
        toastEls.forEach(function (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    });
</script>
@endif
