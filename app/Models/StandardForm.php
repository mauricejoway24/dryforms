<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string title
 * @property string name
 * @property int sort_id
 * @property int form_id
 * @property bool additional_notes_show
 * @property bool footer_text_show
 * @property string footer_text
 */
class StandardForm extends Model
{
    use BelongsToCompany;

    /**
     * @var string
     */
    public $table = 'standard_forms';

    /**
     * @var array
     */
    public $fillable = [
        'form_id',
        'name',
        'title',
        'sort_id',
        'company_id',
        'additional_notes_show',
        'footer_text_show',
        'footer_text',
        'insured_signature',
        'company_signature'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
