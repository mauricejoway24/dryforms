<?php
namespace App\Http\Requests\Companies;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CompanyUpdate extends BaseRequest
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
            'company_id' => 'exists:companies,id',
            'name' => 'required|string|unique:companies,name,'. $this->get('id'),
            'logo' => 'nullable|string',
            'email' => 'required|email|unique:companies,email,'. $this->get('id'),
            'street' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string',
            'phone' => 'required|string',
            'cloud_link' => 'nullable|string',
        ];
    }

    /**
     * @return array
     */
    public function validationData()
    {
        $this->merge(
            [
                'company_id' => $this->route('company')
            ]
        );

        return parent::validationData();
    }
}
