<div>
    @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                <ul class="mb-0 ps-0 list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session()->has('status'))
        <div class="alert alert-success py-2 mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span class="small">{{ session('status') }}</span>
        </div>
    @endif
</div>
