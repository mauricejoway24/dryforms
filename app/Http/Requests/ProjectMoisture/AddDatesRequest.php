<?php
/**
 * Created by PhpStorm.
 * User: nimfus
 * Date: 07.08.2018
 * Time: 21:05
 */

namespace App\Http\Requests\ProjectMoisture;


use App\Http\Requests\BaseRequest;

class AddDatesRequest extends BaseRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'project_id' => ['required', 'integer'],
            'selected_areas' => ['required', 'array'],
            'date_range' => ['required', 'string']
        ];
    }
}