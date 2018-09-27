<?php

namespace App\Models;

use App\Services\Statements\StatementTransformer;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property int form_id
 * @property int project_id
 * @property int company_id
 * @property string title
 * @property string name
 * @property bool additional_notes_show
 * @property bool footer_text_show
 * @property string footer_text
 */
class ProjectForm extends Model
{
    use BelongsToCompany;

    /**
     * @var string
     */
    public $table = 'project_forms';

    /**
     * @var array
     */
    public $fillable = [
    	'form_id',
        'company_id',
        'project_id',
        'title',
        'name',
        'sort_id',
        'additional_notes_show',
        'footer_text_show',
        'footer_text',
        'insured_signature',
        'company_signature',
        'insured_signature_upated_at',
        'company_signature_upated_at'
    ];

    /**
     * @var array
     */
    public $visible = [
        'company_id',
        'form_id',
        'project_id',
        'title',
        'name',
        'sort_id',
        'additional_notes_show',
        'footer_text_show',
        'footer_text',
        'insured_signature',
        'company_signature',
        'insured_signature_upated_at',
        'company_signature_upated_at',
        'updated_at',

        'company',
        'default_form_data',
        'standard_form',
        'project',

        'statements',
        'footer',
        'notes'
    ];

    /**
     * @var array
     */
    public $with = [
        'company',
        'default_form_data',
        'standard_form',
        'project'
    ];

    /**
     * @var array
     */
    public $casts = [
        'additional_notes_show' => 'boolean',
        'footer_text_show' => 'boolean',
    ];

    /**
     * @var array
     */
    public $appends = [
        'statements',
        'footer',
        'notes'
    ];

    /**
     * @return Collection
     */
    public function getStatementsAttribute()
    {
        $statements = [];
        $statementTransformer = new StatementTransformer();
        $rawStatements = ProjectStatement::where('project_id', $this->project_id)
            ->where('form_id', $this->form_id)
            ->get();
        foreach ($rawStatements as $statement) {
            $statement->statement = $statementTransformer->transform($this->project_id, $statement->statement);
            $statements[] = $statement;
        }

        return collect($statements);
    }

    /**
     * @return array
     */
    public function getFooterAttribute()
    {
        $footer = [
            'visible' => $this->footer_text_show,
            'content' => $this->footer_text
        ];

        return $footer;
    }

    /**
     * @return mixed
     */
    public function getNotesAttribute()
    {
        return ProjectDailylog::where([
            'project_id' => $this->project_id,
            'form_id' => $this->form_id
        ])->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function default_form_data()
    {
        return $this->hasOne(DefaultFromData::class, 'form_id', 'form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function standard_form()
    {
        return $this->hasOne(DefaultFromData::class, 'form_id', 'form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
