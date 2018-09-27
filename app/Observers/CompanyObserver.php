<?php

namespace App\Observers;

use App\Models\Company;
use App\Services\Projects\ProjectFormService;
use App\Services\Projects\StandardStatementsService;

class CompanyObserver
{
    const TEMPLATE_COMPANY_ID = -1;

    /**
     * @var ProjectFormService
     */
    private $projectFormService;

    /**
     * @var StandardStatementsService
     */
    private $standardStatementsService;

    /**
     * CompanyObserver constructor.
     * @param ProjectFormService $projectFormService
     * @param StandardStatementsService $standardStatementsService
     */
    public function __construct(ProjectFormService $projectFormService, StandardStatementsService $standardStatementsService)
    {
        $this->projectFormService = $projectFormService;
        $this->standardStatementsService = $standardStatementsService;
    }

    /**
     * @param Company $company
     */
    public function created(Company $company)
    {
        $this->projectFormService->createStandardForms($company);
        $this->standardStatementsService->createDefaultStatements($company);
    }
}
