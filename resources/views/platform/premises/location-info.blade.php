@if($premise->exists)
<div class="card mt-3">
    <div class="card-header">
        <h5>Расположение помещения</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th style="width: 200px;">Жилой комплекс:</th>
                <td>
                    @if($premise->floor && $premise->floor->section && $premise->floor->section->building && $premise->floor->section->building->complex)
                    <a href="{{ route('platform.complex.edit', $premise->floor->section->building->complex) }}">
                        {{ $premise->floor->section->building->complex->name }}
                    </a>
                    @else
                    <span class="text-muted">Не указан</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Здание:</th>
                <td>
                    @if($premise->floor && $premise->floor->section && $premise->floor->section->building)
                    <a href="{{ route('platform.building.edit', $premise->floor->section->building) }}">
                        {{ $premise->floor->section->building->name }}
                    </a>
                    @else
                    <span class="text-muted">Не указано</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Секция:</th>
                <td>
                    @if($premise->floor && $premise->floor->section)
                    <a href="{{ route('platform.section.edit', $premise->floor->section) }}">
                        {{ $premise->floor->section->name }}
                    </a>
                    @else
                    <span class="text-muted">Не указана</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Этаж:</th>
                <td>
                    @if($premise->floor)
                    <a href="{{ route('platform.floor.edit', $premise->floor) }}">
                        {{ $premise->floor->number }}
                    </a>
                    @else
                    <span class="text-muted">Не указан</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Полный адрес:</th>
                <td><strong>{{ $premise->full_address }}</strong></td>
            </tr>
        </table>
    </div>
</div>
@endif
