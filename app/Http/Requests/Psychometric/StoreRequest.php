<?php

namespace App\Http\Requests\Psychometric;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'selected_areas' => ['required', 'array'],
            'date_range' => ['required', 'string']
        ];
    }
}