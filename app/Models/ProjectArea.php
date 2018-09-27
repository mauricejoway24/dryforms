<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

/**
 * @property Project project
 * @property int id
 * @property ProjectPsychometricDays[] psychometric_days
 */
class ProjectArea extends Model
{
    use BelongsToCompany;

    /**
     * @var string
     */
    public $table = 'project_areas';

    /**
     * @var array
     */
    public $fillable = [
    	'project_id',
        'company_id',
        'area_id',
        'overal_square_feet'
    ];

    /**
     * @var array
     */
    public $visible = [
        'id',
        'project_id',
        'company_id',
        'area_id',
        'overal_square_feet',

        'company',
        'project',
        'standard_area',
        'psychometric_days'
    ];

    public $with = [
        'standard_area'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function standard_area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function psychometric_days()
    {
        return $this->hasMany(ProjectPsychometricDays::class, 'area_id', 'id');
    }
}
