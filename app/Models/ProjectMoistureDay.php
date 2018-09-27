<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMoistureDay extends Model
{
    /**
     * @var string
     */
    public $table = 'moisture_form_days';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
    	'moisture_form_id',
        'date',
    ];

    /**
     * @var array
     */
    public $with = [
        'days_data'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function moisture_form()
    {
        return $this->belongsTo(ProjectMoistureForm::class, 'moisture_form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function days_data()
    {
        return $this->hasMany(ProjectMoistureDayData::class, 'day_id');
    }
}
