<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ReviewRequestMessage extends Model
{
    use BelongsToCompany;
    /**
     * @var string
     */
    public $table = 'review_request_messages';

    /**
     * @var array
     */
    public $fillable = [
        'company_id',
        'message'
    ];

    /**
     * @var array
     */
    public $visible = [
    	'company_id',
        'message'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
