<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 */
class Form extends Model
{
    const MOISTURE_FORM_ID = 7;

    /**
     * @var string
     */
    public $table = 'forms';

    /**
     * @var array
     */
    public $fillable = [
        'name',
        'company_id'
    ];

    /**
     * @var array
     */
    public $visible = [
        'id',
        'name',
        'company_id',
        'default_data'
    ];
}