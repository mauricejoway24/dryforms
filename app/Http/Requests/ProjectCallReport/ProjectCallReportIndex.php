<?php

namespace App\Http\Requests\ProjectCallReport;

use App\Http\Requests\BaseRequest;

class ProjectCallReportIndex extends BaseRequest
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
            'project_id' => ['required', 'exists:projects,id']
        ];
    }

    /**
     * @return array
     */
    public function validationData()
    {
        $this->merge(
            [
                'project_id' => $this->route('project')
            ]
        );

        return parent::validationData();
    }
}
