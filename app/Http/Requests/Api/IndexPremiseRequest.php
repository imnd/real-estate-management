<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class IndexPremiseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complex_id' => 'nullable|integer|exists:complexes,id',
            'building_id' => 'nullable|integer|exists:buildings,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'floor_id' => 'nullable|integer|exists:floors,id',
            'status' => 'nullable|string|in:available,reserved,sold,not_for_sale',
            'type' => 'nullable|string|in:apartment,studio,penthouse,commercial',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0|gt:price_from',
            'area_from' => 'nullable|numeric|min:0',
            'area_to' => 'nullable|numeric|min:0|gt:area_from',
            'rooms' => 'nullable|integer|min:1|max:10',
            'sort_by' => 'nullable|string|in:price,area,rooms,created_at,updated_at,floor',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'features' => 'nullable|array',
            'features.*' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'complex_id.exists' => 'Выбранный комплекс не существует',
            'building_id.exists' => 'Выбранное здание не существует',
            'section_id.exists' => 'Выбранная секция не существует',
            'floor_id.exists' => 'Выбранный этаж не существует',
            'status.in' => 'Недопустимый статус',
            'type.in' => 'Недопустимый тип помещения',
            'price_from.min' => 'Цена от должна быть положительным числом',
            'price_to.min' => 'Цена до должна быть положительным числом',
            'price_to.gt' => 'Цена до должна быть больше цены от',
            'area_from.min' => 'Площадь от должна быть положительным числом',
            'area_to.min' => 'Площадь до должна быть положительным числом',
            'area_to.gt' => 'Площадь до должна быть больше площади от',
            'rooms.min' => 'Количество комнат должно быть не менее 1',
            'rooms.max' => 'Количество комнат должно быть не более 10',
            'per_page.min' => 'Количество элементов на странице должно быть не менее 1',
            'per_page.max' => 'Количество элементов на странице должно быть не более 100',
        ];
    }
}
