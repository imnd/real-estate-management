<div class="card mb-3 h-100">
    <div class="card-body">
        <h5 class="text-muted fw-light">Жилые комплексы</h5>
        <div class="d-flex align-items-center mt-2">
            <h2 class="fw-bold mb-0">{{ $total_complexes }}</h2>
            <span class="ms-2 text-muted">всего</span>
        </div>
        <hr>
        <div class="row">
            @foreach($complex_stats as $stat)
            <div class="col-6 mb-2">
                <small class="text-muted d-block text-uppercase">{{ $stat->status }}</small>
                <span class="fw-bold">{{ $stat->count }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
