<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInstrument extends Model
{
    /**
     * @var string
     */
    public $table = 'project_instruments';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'make',
        'model',
        'project_id',
        'company_id'
    ];

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
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
