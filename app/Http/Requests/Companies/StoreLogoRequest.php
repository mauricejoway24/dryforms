<?php
namespace App\Http\Requests\Companies;

use App\Http\Requests\BaseRequest;

class StoreLogoRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo' => ['required', 'file'],
        ];
    }
}
