<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property ProjectMoistureDay days
 */
class ProjectMoistureForm extends Model
{
    /**
     * @var string
     */
    public $table = 'moisture_forms';


    /**
     * @var array
     */
    public $fillable = [
        'project_id'
    ];

    /**
     * @var array
     */
    public $with = [
        'project',
        'days'
    ];

    /**
     * @param int $projectId
     * @return ProjectMoistureForm
     */
    public function createDefault(int $projectId): self
    {
        return $this->create([
            'project_id' => $projectId
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function days()
    {
        return $this->hasMany(ProjectMoistureDay::class, 'moisture_form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
