<?php

namespace App\Http\Requests\Equipments;

use App\Http\Requests\BaseRequest;

class EquipmentUpdate extends BaseRequest
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
            'project_id' => 'nullable|exists:projects,id',
            'category_id' => 'sometimes|required|exists:equipment_categories,id',
            'model_id' => 'sometimes|required|exists:equipment_models,id',
            'equipment_id' => 'exists:equipments,id',
            'status_id' => 'exists:equipment_statuses,id',
            'location' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
            'serial' => 'sometimes|required|integer',
            'company_id' => 'required|exists:companies,id'
        ];
    }

    /**
     * @return array
     */
    public function validationData()
    {
        $this->merge(
            [
                'equipment_id' => $this->route('equipment')
            ]
        );

        return parent::validationData();
    }
}
