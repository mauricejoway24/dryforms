<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMoistureDayData extends Model
{
    const DEFAULT_DATA_SET = [
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ],
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ],
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ],
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ],
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ],
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ],
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ],
        [
            'structure' => null,
            'material' => null,
            'value' => null
        ]
    ];

    /**
     * @var string
     */
    public $table = 'moisture_form_day_data';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
    	'day_id',
        'area_id',
        'data'
    ];

    /**
     * @var array
     */
    public $with = [
        'area'
    ];

    /**
     * @param int $dayId
     * @param int $areaId
     * @return self
     */
    public function createDefault(int $dayId, int $areaId): self
    {
        return $this->create([
            'day_id' => $dayId,
            'area_id' => $areaId,
            'data' => static::DEFAULT_DATA_SET
        ]);
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getDataAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * @param $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(ProjectArea::class, 'area_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function day()
    {
        return $this->belongsTo(ProjectMoistureDay::class, 'day_id');
    }
}
