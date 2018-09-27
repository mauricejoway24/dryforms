<?php

namespace App\Services\Projects;

use App\Models\Company;
use App\Models\DefaultStatement;
use App\Models\Form;
use App\Models\ProjectForm;
use App\Models\ProjectMoistureForm;
use App\Models\ProjectStatement;
use App\Models\StandardForm;
use App\Models\StandardStatement;
use App\Observers\CompanyObserver;
use Illuminate\Support\Collection;

class ProjectFormService
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var ProjectForm
     */
    private $projectForm;

    /**
     * @var ProjectMoistureForm
     */
    private $projectMoistureForm;

    /**
     * @var DefaultStatement
     */
    private $defaultStatement;

    /**
     * @var StandardStatement
     */
    private $standardStatement;

    /**
     * @var ProjectStatement
     */
    private $projectStatement;

    /**
     * @var StandardForm
     */
    private $standardForm;

    /**
     * ProjectFormService constructor.
     * @param Form $form
     * @param ProjectForm $projectForm
     * @param StandardForm $standardForm
     * @param ProjectMoistureForm $projectMoistureForm
     * @param DefaultStatement $defaultStatement
     * @param StandardStatement $standardStatement
     * @param ProjectStatement $projectStatement
     */
    public function __construct(
        Form $form,
        ProjectForm $projectForm,
        StandardForm $standardForm,
        ProjectMoistureForm $projectMoistureForm,
        DefaultStatement $defaultStatement,
        StandardStatement $standardStatement,
        ProjectStatement $projectStatement
    ) {
        $this->form = $form;
        $this->projectForm = $projectForm;
        $this->standardForm = $standardForm;
        $this->projectMoistureForm = $projectMoistureForm;
        $this->defaultStatement = $defaultStatement;
        $this->standardStatement = $standardStatement;
        $this->projectStatement = $projectStatement;
    }

    /**
     * @param int $projectId
     * @param int $companyId
     * @param array $formIds
     */
    public function generateProjectForms(int $projectId, int $companyId, array $formIds)
    {
        $forms = $this->getForms($formIds, $companyId);
        foreach ($forms as $form) {
            $this->createProjectForm($projectId, $companyId, $form);
        }
    }

    /**
     * @param int $projectId
     * @param int $companyId
     * @param StandardForm $form
     */
    private function createProjectForm(int $projectId, int $companyId, StandardForm $form): void
    {
        switch($form->id) {
            case Form::MOISTURE_FORM_ID:
                $this->projectMoistureForm->createDefault($projectId);
                $this->projectForm->create([
                    'form_id' => $form->form_id,
                    'company_id' => $companyId,
                    'project_id' => $projectId,
                    'title' => $form->title,
                    'name' => $form->name,
                    'sort_id' => $form->sort_id,
                    'additional_notes_show' => $form->additional_notes_show,
                    'footer_text_show' => $form->footer_text_show,
                    'footer_text' => $form->footer_text,
                ]);
                break;
            default:
                $projectForm = $this->projectForm->create([
                    'form_id' => $form->form_id,
                    'company_id' => $companyId,
                    'project_id' => $projectId,
                    'title' => $form->title,
                    'name' => $form->name,
                    'sort_id' => $form->sort_id,
                    'additional_notes_show' => $form->additional_notes_show,
                    'footer_text_show' => $form->footer_text_show,
                    'footer_text' => $form->footer_text,
                ]);
                $this->setStatements($projectForm);
                break;
        }
    }

    /**
     * @param Company $company
     * @return mixed
     */
    public function createStandardForms(Company $company)
    {
        $forms = $this->form->where('company_id', CompanyObserver::TEMPLATE_COMPANY_ID)->get();
        foreach ($forms as $index => $form) {
            $this->standardForm->create([
                'form_id' => $form->id,
                'name' => $form->name,
                'title' => $form->name,
                'sort_id' => $index + 1,
                'company_id' => $company->id
            ]);
        }

        return $this->standardForm->where('company_id', $company->id)->orderBy('sort_id')->get();
    }

    /**
     * @param array $ids
     * @param int $companyId
     * @return Collection
     */
    private function getForms(array $ids, int $companyId): Collection
    {
        return $this->standardForm->where('company_id', $companyId)->whereIn('form_id', array_pluck($ids, 'form_id'))->get();
    }

    /**
     * TODO refactor
     * @param ProjectForm $form
     */
    private function setStatements(ProjectForm $form): void
    {
        $standardStatements = $this->standardStatement
            ->where('form_id', $form->form_id)
            ->get();
        if (!$standardStatements) {
            $defaultStatements = $this->defaultStatement
                ->where('form_id', $form->form_id)
                ->get();
            foreach ($defaultStatements as $defaultStatement) {
                $this->standardStatement->create([
                    'company_id' => $form->company_id,
                    'form_id' => $defaultStatement->form_id,
                    'title' => $defaultStatement->title,
                    'statement' => $defaultStatement->statement
                ]);

                $this->projectStatement->create([
                    'company_id' => $form->company_id,
                    'project_id' => $form->project_id,
                    'form_id' => $defaultStatement->form_id,
                    'title' => $defaultStatement->title,
                    'statement' => $defaultStatement->statement
                ]);
            }
        } else {
            foreach ($standardStatements as $standardStatement) {
                $this->projectStatement->create([
                    'company_id' => $form->company_id,
                    'project_id' => $form->project_id,
                    'form_id' => $standardStatement->form_id,
                    'title' => $standardStatement->title,
                    'statement' => $standardStatement->statement
                ]);
            }
        }

    }
}
