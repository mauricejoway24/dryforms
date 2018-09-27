<?php
namespace App\Http\Requests\ProjectMoisture;

use App\Http\Requests\BaseRequest;

class ProjectMoistureDateUpdate extends BaseRequest
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
            'date' => ['required', 'date']
        ];
    }
}