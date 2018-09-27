<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ReviewLink extends Model
{
    use BelongsToCompany;
    /**
     * @var string
     */
    public $table = 'review_links';

    /**
     * @var array
     */
    public $fillable = [
        'company_id',
        'url',
        'activate'
    ];

    /**
     * @var array
     */
    public $visible = [
        'id',
        'company_id',
    	'url',
        'activate'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
