<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 */
class Company extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

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
        'cloud_link',
        'dbx_token'
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
        'cloud_link',
        'dbx_token'
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
     * Relation with employees.
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * @return null|string
     */
    public function getPublicLogoPathAttribute()
    {
        return $this->logo ? "storage/$this->logo" : null;
    }
}
