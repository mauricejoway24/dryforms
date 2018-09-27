<?php

namespace App\Http\Requests\Psychometric;

use App\Http\Requests\BaseRequest;

class UpdateTimeRequest extends BaseRequest
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
            'old_time' => ['required', 'date'],
            'new_time' => ['required', 'date', 'different:old_time'],
            'project_id' => ['required', 'integer'],
        ];
    }
}