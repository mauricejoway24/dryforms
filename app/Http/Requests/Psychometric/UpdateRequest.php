<?php

namespace App\Http\Requests\Psychometric;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
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
            'contaminants_quantity' => ['required', 'integer']
        ];
    }
}