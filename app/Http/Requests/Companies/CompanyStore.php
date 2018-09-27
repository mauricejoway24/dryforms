<?php
namespace App\Http\Requests\Companies;

use App\Http\Requests\BaseRequest;

class CompanyStore extends BaseRequest
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
            'name' => ['required', 'string', 'unique:companies,name'],
            'logo' => ['nullable', 'string'],
            'email' => ['required', 'email', 'unique:companies,email'],
            'street' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'cloud_link' => ['nullable', 'string'],
        ];
    }
}
