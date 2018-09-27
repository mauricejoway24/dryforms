<?php

namespace App\Http\Requests\Psychometric;

use App\Http\Requests\BaseRequest;

class UpdateMeasurementRequest extends BaseRequest
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
            'outside' => ['required', 'array'],
            'unaffected' => ['required', 'array'],
            'affected' => ['required', 'array'],
            'dehumidifier' => ['required', 'array'],
            'hvac' => ['required', 'array'],
            'update_all_measurements' => ['nullable', 'boolean'],
            'project_id' => ['required_if:update_all_measurements,true']
        ];
    }
}