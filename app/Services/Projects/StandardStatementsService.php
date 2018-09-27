<?php

namespace App\Services\Projects;

use App\Models\Company;
use App\Models\DefaultStatement;
use App\Models\StandardStatement;

class StandardStatementsService
{
    /**
     * @var DefaultStatement
     */
    private $defaultStatement;

    /**
     * @var StandardStatement
     */
    private $standardStatement;

    /**
     * StandardStatementsService constructor.
     * @param DefaultStatement $defaultStatement
     * @param StandardStatement $standardStatement
     */
    public function __construct(DefaultStatement $defaultStatement, StandardStatement $standardStatement)
    {
        $this->defaultStatement = $defaultStatement;
        $this->standardStatement = $standardStatement;
    }

    /**
     * @param Company $company
     */
    public function createDefaultStatements(Company $company)
    {
        $defaultStatements = $this->defaultStatement->all();
        foreach ($defaultStatements as $defaultStatement) {
            $this->standardStatement->create([
                'company_id' => $company->id,
                'form_id' => $defaultStatement->form_id,
                'title' => $defaultStatement->title,
                'statement' => $defaultStatement->statement,
            ]);
        }
    }
}
