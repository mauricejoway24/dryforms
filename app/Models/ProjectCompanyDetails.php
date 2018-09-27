<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCompanyDetails extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'project_company_details';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'logo',
        'name',
        'email',
        'street',
        'city',
        'state',
        'zip',
        'phone',
        'project_id'
    ];

    protected $visible = [
        'id',
        'logo',
        'public_logo_path',
        'name',
        'email',
        'street',
        'city',
        'state',
        'zip',
        'phone',
        'project_id'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'public_logo_path'
    ];

    /**
     * Relation with user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return null|string
     */
    public function getPublicLogoPathAttribute()
    {
        return $this->logo ? "storage/$this->logo" : null;
    }
}
