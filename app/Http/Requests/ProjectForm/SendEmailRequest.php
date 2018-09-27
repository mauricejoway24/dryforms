<?php
namespace App\Http\Requests\ProjectForm;

use App\Http\Requests\BaseRequest;

class SendEmailRequest extends BaseRequest
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
            'pdf_type' => ['required', 'in:multiple,single'],
            'recipients' => ['required', 'array'],
            'project_id' => ['required', 'exists:projects,id'],
            'forms' => ['required', 'array']
        ];
    }
}
