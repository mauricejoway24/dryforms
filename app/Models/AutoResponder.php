<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoResponder extends Model
{
    /**
     * @var string
     */
    public $table = 'auto_responders';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'slug',
        'title',
        'content',
        'active'
    ];

    /**
     * @var array
     */
    public $visible = [
        'id',
        'slug',
        'title',
        'content',
        'active'
    ];

    public $casts = [
        'active' => 'boolean'
    ];
}