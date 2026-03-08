<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePremiseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'floor_id' => 'required|integer|exists:floors,id',
            'number' => 'required|string|max:50|unique:premises,number,NULL,id,floor_id,' . $this->floor_id,
            'type' => 'required|string|in:apartment,studio,penthouse,commercial',
            'rooms' => 'required|integer|min:1|max:10',
            'total_area' => 'required|numeric|min:1|max:1000',
            'living_area' => 'nullable|numeric|min:0|max:' . ($this->total_area ?? 1000),
            'kitchen_area' => 'nullable|numeric|min:0|max:' . ($this->total_area ?? 1000),
            'status' => 'required|string|in:available,reserved,sold,not_for_sale',
            'base_price' => 'required|numeric|min:0|max:99999999999',
            'discount_price' => 'nullable|numeric|min:0|lt:base_price',
            'floor' => 'required|integer|min:1|max:200',
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
            'floor_id.required' => 'Необходимо указать этаж',
            'floor_id.exists' => 'Указанный этаж не существует',
            'number.required' => 'Необходимо указать номер помещения',
            'number.unique' => 'Помещение с таким номером уже существует на этом этаже',
            'type.required' => 'Необходимо указать тип помещения',
            'type.in' => 'Недопустимый тип помещения',
            'rooms.required' => 'Необходимо указать количество комнат',
            'rooms.min' => 'Количество комнат должно быть не менее 1',
            'total_area.required' => 'Необходимо указать общую площадь',
            'total_area.min' => 'Общая площадь должна быть не менее 1 кв.м',
            'total_area.max' => 'Общая площадь должна быть не более 1000 кв.м',
            'living_area.max' => 'Жилая площадь не может превышать общую площадь',
            'kitchen_area.max' => 'Площадь кухни не может превышать общую площадь',
            'status.required' => 'Необходимо указать статус',
            'status.in' => 'Недопустимый статус',
            'base_price.required' => 'Необходимо указать базовую цену',
            'base_price.min' => 'Базовая цена должна быть положительным числом',
            'discount_price.lt' => 'Цена со скидкой должна быть меньше базовой цены',
            'floor.required' => 'Необходимо указать этаж',
            'floor.min' => 'Этаж должен быть не менее 1',
            'attachments.*.image' => 'Файл должен быть изображением',
            'attachments.*.mimes' => 'Разрешены только форматы: jpeg, png, jpg, gif',
            'attachments.*.max' => 'Размер файла не должен превышать 2MB',
        ];
    }
}
