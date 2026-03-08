<div class="card mt-3">
    <div class="card-header">
        <h5>Помещения на этаже</h5>
    </div>
    <div class="card-body">
        @if($floor->exists && $floor->premises->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>№ помещения</th>
                    <th>Кол-во комнат</th>
                    <th>Площадь</th>
                    <th>Цена</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($floor->premises->sortBy('number') as $premise)
                <tr>
                    <td>{{ $premise->number }}</td>
                    <td>{{ $premise->rooms }}</td>
                    <td>{{ number_format($premise->area, 2) }} м²</td>
                    <td>{{ $premise->price ? number_format($premise->price, 2) . ' ₽' : 'Не указана' }}</td>
                    <td>
                                    <span class="badge bg-{{ $premise->status_color }}">
                                        {{ $premise->status_label }}
                                    </span>
                    </td>
                    <td>
                        <a href="{{ route('platform.premise.edit', $premise) }}"
                           class="btn btn-sm btn-primary">
                            <i class="icon-pencil"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <a href="{{ route('platform.premise.create', ['floor_id' => $floor->id]) }}"
               class="btn btn-success">
                <i class="icon-plus"></i> Добавить помещение
            </a>
        </div>
        @elseif($floor->exists)
        <p class="text-muted">На этом этаже пока нет помещений</p>
        <a href="{{ route('platform.premise.create', ['floor_id' => $floor->id]) }}"
           class="btn btn-success">
            <i class="icon-plus"></i> Добавить первое помещение
        </a>
        @else
        <p class="text-muted">Сохраните этаж, чтобы добавить помещения</p>
        @endif
    </div>
</div>
