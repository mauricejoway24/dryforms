<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ProjectMoistureForm moisture_form
 * @property ProjectArea[] areas
 * @property ProjectCallReport call_report
 * @property int company_id
 */
class Project extends Model
{
    use BelongsToCompany;

    /**
     * @var string
     */
    public $table = 'projects';

    /**
     * @var array
     */
    public $fillable = [
        'company_id',
        'owner_name',
        'assigned_to',
        'address',
        'phone',
        'status'
    ];

    /**
     * @var array
     */
    public $visible = [
        'id',
        'company_id',
        'owner_name',
        'assigned_to',
        'address',
        'phone',
        'status',
        'created_at',

        'company',
        'assignee',
        'status_info',
        'areas',
        'company_details',
        'instrument',
        'equipment',
    ];

    /**
     * @var array
     */
    public $with = [
        'company',
        'assignee',
        'status_info',
        'company_details',
        'instrument',
        'equipment',
        'equipment.status',
        'equipment.model',
        'equipment.team',
        'equipment.model.category',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function company_details()
    {
        return $this->hasOne(ProjectCompanyDetails::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forms()
    {
        return $this->hasMany(ProjectForm::class, 'project_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee()
    {
        return $this->belongsTo(Team::class, 'assigned_to');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status_info()
    {
        return $this->belongsTo(ProjectStatus::class, 'status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function areas()
    {
        return $this->hasMany(ProjectArea::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project_call_report()
    {
        return $this->hasOne(ProjectCallReport::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function moisture_form()
    {
        return $this->hasOne(ProjectMoistureForm::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function instrument()
    {
        return $this->hasOne(ProjectInstrument::class, 'project_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function call_report()
    {
        return $this->hasOne(ProjectCallReport::class, 'project_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'project_id');
    }
}
