<?php

namespace App\Http\Requests\ProjectForm;

use App\Http\Requests\BaseRequest;

class ProjectFormStore extends BaseRequest
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
            'company_id' => ['required', 'exists:companies,id'],
            'project_id' => ['required', 'exists:projects,id'],
            'project_forms.*.form_id' => ['required', 'exists:forms,id']
        ];
    }
}
