<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProjectCallReport extends Model
{
    use BelongsToCompany;

    /**
     * @var string
     */
    public $table = 'project_call_reports';

    /**
     * @var array
     */
    public $fillable = [
        'company_id',
        'project_id',
        'contact_name',
        'contact_phone',
        'site_phone',
        'date_contacted',
        'time_contacted',
        'date_loss',
        'point_loss',
        'date_completed',
        'category',
        'class',
        'job_address',
        'city',
        'state',
        'zip_code',
        'cross_streets',
        'apartment_name',
        'building_no',
        'apartment_no',
        'gate_code',
        'assigned_to',
        'is_residential',
        'is_commercial',
        'is_insured',
        'is_tenant',
        'is_water',
        'is_sewage',
        'is_mold',
        'is_fire',
        'insured_name',
        'billing_address',
        'insured_city',
        'insured_state',
        'insured_zip_code',
        'insured_home_phone',
        'insured_cell_phone',
        'insured_work_phone',
        'insured_email',
        'insured_fax',
        'insurance_claim_no',
        'insurance_company',
        'insurance_policy_no',
        'insurance_deductible',
        'insurance_adjuster',
        'insurance_address',
        'insurance_city',
        'insurance_state',
        'insurance_zip_code',
        'insurance_work_phone',
        'insurance_cell_phone',
        'insurance_email',
        'insurance_fax'
    ];

    /**
     * @var array
     */
    public $visible = [
        'id',
        'company_id',
        'project_id',
        'contact_name',
        'contact_phone',
        'site_phone',
        'date_contacted',
        'time_contacted',
        'date_loss',
        'point_loss',
        'date_completed',
        'category',
        'class',
        'job_address',
        'city',
        'state',
        'zip_code',
        'cross_streets',
        'apartment_name',
        'building_no',
        'apartment_no',
        'gate_code',
        'assigned_to',
        'is_residential',
        'is_commercial',
        'is_insured',
        'is_tenant',
        'is_water',
        'is_sewage',
        'is_mold',
        'is_fire',
        'insured_name',
        'billing_address',
        'insured_city',
        'insured_state',
        'insured_zip_code',
        'insured_home_phone',
        'insured_cell_phone',
        'insured_work_phone',
        'insured_email',
        'insured_fax',
        'insurance_claim_no',
        'insurance_company',
        'insurance_policy_no',
        'insurance_deductible',
        'insurance_adjuster',
        'insurance_address',
        'insurance_city',
        'insurance_state',
        'insurance_zip_code',
        'insurance_work_phone',
        'insurance_cell_phone',
        'insurance_email',
        'insurance_fax',

        'company',
        'project',
        'assignee',
        'full_job_address'
    ];

    /**
     * @var array
     */
    public $with = [
        'company',
        'project',
        'assignee'
    ];

    /**
     * @return null|string
     */
    public function getCompanyDetailsNameAttribute(): ?string
    {
        return $this->project->company_details ? $this->project->company_details->name : null;
    }

    /**
     * @return null|string
     */
    public function getCompanyDetailsPhoneAttribute(): ?string
    {
        return $this->project->company_details ? $this->project->company_details->phone : null;
    }

    /**
     * @return null|string
     */
    public function getAssigneeNameAttribute(): ?string
    {
        return $this->assignee ? $this->assignee->name : null;
    }

    /**
     * @return null|string
     */
    public function getFormattedDateLossAttribute(): ?string
    {
        return $this->date_loss ? Carbon::createFromFormat('Y-m-d', $this->date_loss)->format('m/d/Y') : null;
    }

    /**
     * @return null|string
     */
    public function getFormattedDateContactedAttribute(): ?string
    {
        return $this->date_contacted ? Carbon::createFromFormat('Y-m-d', $this->date_contacted)->format('m/d/Y') : null;
    }

    /**
     * @return null|string
     */
    public function getFormattedDateCompletedAttribute(): ?string
    {
        return $this->date_completed ? Carbon::createFromFormat('Y-m-d', $this->date_completed)->format('m/d/Y') : null;
    }

    /**
     * @return null
     */
    public function getFullJobAddressAttribute(): string
    {
        $jobAddress = $this->job_address ? "$this->job_address, " : '';
        $city = $this->city ? "$this->city, " : '';
        $state = $this->state ? "$this->state, " : '';
        $zip = $this->zip_code ? "$this->zip_code" : '';

        return "{$jobAddress}{$city}{$state}{$zip}";
    }

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
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee()
    {
        return $this->belongsTo(Team::class, 'assigned_to');
    }
}
