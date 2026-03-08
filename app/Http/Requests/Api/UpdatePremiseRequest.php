<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePremiseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $premise = $this->route('premise');

        return [
            'floor_id' => 'sometimes|integer|exists:floors,id',
            'number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('premises')->ignore($premise)->where(function ($query) {
                    return $query->where('floor_id', $this->floor_id ?? $this->premise->floor_id);
                }),
            ],
            'type' => 'sometimes|string|in:apartment,studio,penthouse,commercial',
            'rooms' => 'sometimes|integer|min:1|max:10',
            'total_area' => 'sometimes|numeric|min:1|max:1000',
            'living_area' => 'nullable|numeric|min:0|max:' . ($this->total_area ?? $this->premise->total_area),
            'kitchen_area' => 'nullable|numeric|min:0|max:' . ($this->total_area ?? $this->premise->total_area),
            'status' => 'sometimes|string|in:available,reserved,sold,not_for_sale',
            'base_price' => 'sometimes|numeric|min:0|max:999999999',
            'discount_price' => 'nullable|numeric|min:0|lt:base_price',
            'floor' => 'sometimes|integer|min:1|max:200',
            'layout_image' => 'nullable|string|max:255',
            'additional_features' => 'nullable|array',
            'additional_features.balcony' => 'nullable|boolean',
            'additional_features.loggia' => 'nullable|boolean',
            'additional_features.view' => 'nullable|string|in:yard,street,park,river,sea',
            'additional_features.parking' => 'nullable|boolean',
            'additional_features.furniture' => 'nullable|boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'floor_id.exists' => 'Указанный этаж не существует',
            'number.unique' => 'Помещение с таким номером уже существует на этом этаже',
            'type.in' => 'Недопустимый тип помещения',
            'rooms.min' => 'Количество комнат должно быть не менее 1',
            'total_area.min' => 'Общая площадь должна быть не менее 1 кв.м',
            'total_area.max' => 'Общая площадь должна быть не более 1000 кв.м',
            'living_area.max' => 'Жилая площадь не может превышать общую площадь',
            'kitchen_area.max' => 'Площадь кухни не может превышать общую площадь',
            'status.in' => 'Недопустимый статус',
            'base_price.min' => 'Базовая цена должна быть положительным числом',
            'discount_price.lt' => 'Цена со скидкой должна быть меньше базовой цены',
            'floor.min' => 'Этаж должен быть не менее 1',
            'attachments.*.image' => 'Файл должен быть изображением',
            'attachments.*.mimes' => 'Разрешены только форматы: jpeg, png, jpg, gif',
            'attachments.*.max' => 'Размер файла не должен превышать 2MB',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('additional_features') && is_string($this->additional_features)) {
            $this->merge([
                'additional_features' => json_decode($this->additional_features, true)
            ]);
        }
    }
}
