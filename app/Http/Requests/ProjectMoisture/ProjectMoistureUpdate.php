<?php
namespace App\Http\Requests\ProjectMoisture;

use App\Http\Requests\BaseRequest;

class ProjectMoistureUpdate extends BaseRequest
{
    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'integer'],
            'area_id' => ['required', 'integer'],
            'day_id' => ['required', 'integer'],
            'data' => ['required', 'array'],
            'data.*.material' => ['present'],
            'data.*.structure' => ['present'],
            'data.*.value' => ['present'],
        ];
    }
}