<?php
namespace App\Http\Requests\AutoResponders;

use App\Http\Requests\BaseRequest;

class AutoResponderUpdate extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'content' => ['required', 'string']
        ];
    }
}