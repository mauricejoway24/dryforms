<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    const IN_PROGRESS = 1;
    const COMPLETED = 2;
    const DELETED = 3;

    /**
     * @var string
     */
    public $table = 'project_status';

    /**
     * @var array
     */
    public $fillable = [
        'name'
    ];

    /**
     * @var array
     */
    public $visible = [
    	'id',
        'name'
    ];
}
