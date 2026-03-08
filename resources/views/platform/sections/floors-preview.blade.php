<div class="card mt-3">
    <div class="card-header">
        <h5>Этажи в секции</h5>
    </div>
    <div class="card-body">
        @if($section->exists && $section->floors->count() > 0)
        <div class="row">
            @foreach($section->floors->sortBy('number') as $floor)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6>Этаж {{ $floor->number }}</h6>
                        <p class="mb-1">Помещений: {{ $floor->premises->count() }}</p>
                        <p class="mb-1">Площадь: {{ $floor->total_area }} м²</p>
                        <small class="text-muted">
                            Доступно: {{ $floor->premises->where('status', 'available')->count() }}
                        </small>
                        <div class="mt-2">
                            <a href="{{ route('platform.floor.edit', $floor) }}"
                               class="btn btn-sm btn-primary">
                                Управлять
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-3">
            <a href="{{ route('platform.floor.create', ['section_id' => $section->id]) }}"
               class="btn btn-success">
                <i class="icon-plus"></i> Добавить этаж
            </a>
        </div>
        @elseif($section->exists)
        <p class="text-muted">В этой секции пока нет этажей</p>
        <a href="{{ route('platform.floor.create', ['section_id' => $section->id]) }}"
           class="btn btn-success">
            <i class="icon-plus"></i> Добавить первый этаж
        </a>
        @else
        <p class="text-muted">Сохраните секцию, чтобы добавить этажи</p>
        @endif
    </div>
</div>
