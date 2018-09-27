<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProjectForm\SendEmailRequest;
use App\Models\Project;
use App\Models\ProjectForm;
use App\Models\StandardForm;
use App\Services\Forms\FormsEmailService;
use App\Services\Forms\FormsPdfService;
use App\Services\Projects\ProjectFormService;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectForm\ProjectFormIndex;
use App\Http\Requests\ProjectForm\ProjectFormUpdate;
use App\Http\Requests\ProjectForm\ProjectFormStore;
use App\Http\Requests\ProjectForm\ProjectFormSignatureUpdate;

class ProjectFormsController extends ApiController
{
    /**
     * @var ProjectForm
     */
    private $projectForm;

    /**
     * @var StandardForm
     */
    private $standardForm;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    /**
     * @var ProjectFormService
     */
    private $projectFormService;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var FormsPdfService
     */
    private $formsPdfService;

    /**
     * @var FormsEmailService
     */
    private $formsEmailService;

    /**
     * ProjectFormsController constructor.
     * @param StandardForm $standardForm
     * @param ProjectForm $projectForm
     * @param JWTAuth $jwtAuth
     * @param ProjectFormService $projectFormService
     * @param Project $project
     * @param FormsPdfService $formsPdfService
     * @param FormsEmailService $formsEmailService
     */
    public function __construct(
        StandardForm $standardForm,
        ProjectForm $projectForm,
        JWTAuth $jwtAuth,
        ProjectFormService $projectFormService,
        Project $project,
        FormsPdfService $formsPdfService,
        FormsEmailService $formsEmailService
    )
    {
        $this->project = $project;
        $this->projectForm = $projectForm;
        $this->standardForm = $standardForm;
        $this->jwtAuth = $jwtAuth;
        $this->projectFormService = $projectFormService;
        $this->formsPdfService = $formsPdfService;
        $this->formsEmailService = $formsEmailService;
    }

    /**
     * @param ProjectFormIndex $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProjectFormIndex $request): JsonResponse
    {
        $project = $this->project->find($request->get('project_id'));

        return $this->respond($project->forms);
    }

    /**
     * @param ProjectFormStore $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProjectFormStore $request): JsonResponse
    {
        $this->projectFormService->generateProjectForms(
            $request->get('project_id'),
            $request->get('company_id'),
            $request->get('project_forms')
        );

        return $this->respond(['message' => 'Project Forms successfully created']);
    }

    /**
     * @param int $projectId
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $projectId, Request $request)
    {
        $form = $this->projectForm->where([
            'project_id' => $projectId,
            'form_id' => $request->get('id')
        ])->first();

        return $this->respond(['form' => $form]);
    }

    /**
     * @param ProjectFormUpdate $request
     *
     * @return JsonResponse
     */
    public function update(ProjectFormUpdate $request): JsonResponse
    {
        $queryParams = $request->validatedOnly();
        $projectID = $queryParams['project_id'];
        $oldProjectForms = $this->projectForm
            ->where('project_id', $projectID)
            ->get();
        $newProjectForms = $queryParams['project_forms'];
        foreach ($oldProjectForms as $key => $oldProjectForm) {
            $oldFormID = $oldProjectForm->form_id;
            $found = array_filter($newProjectForms, function ($newProjectForm) use ($oldFormID) {
                return $newProjectForm['form_id'] == $oldFormID;
            });
            if (count($found) === 0) {
                $oldProjectForm->delete();
            }
        }
        foreach ($newProjectForms as $key => $newProjectForm) {
            $isExist = $this->projectForm
                ->where('project_id', $projectID)
                ->where('form_id', $newProjectForm['form_id'])
                ->exists();
            if (!$isExist) {
                $this->projectForm->create([
                    'form_id' => $newProjectForm['form_id'],
                    'company_id' => auth()->user()->company_id,
                    'project_id' => $projectID
                ]);
            }
        }
        return $this->respond(['message' => 'Project Forms successfully updated']);
    }

    /**
     * @param ProjectFormSignatureUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setSignature(ProjectFormSignatureUpdate $request)
    {
        $project_id = $request->input('project_id');
        $form_id = $request->input('form_id');
        $queryParams = $request->validatedOnly();
        unset($queryParams['id']);
        $projectForm = $this->projectForm
            ->where([
                'project_id' => $project_id,
                'form_id' => $form_id
            ])->update($queryParams);

        return $this->respond(['message' => 'Project Signature successfully updated', 'projectForm' => $projectForm]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return array|\LynX39\LaraPdfMerger\PDF|string
     * @throws \Exception
     */
    public function print(int $id, Request $request)
    {
        $this->jwtAuth->setToken($request->get('token'));
        $this->jwtAuth->authenticate();
        $project = $this->project->findOrFail($id);
        $forms = $this->projectForm
            ->where([
                'company_id' => auth()->user()->company_id,
                'project_id' => $project->id
            ])
            ->whereIn('form_id', explode(',', $request->get('forms')))
            ->orderBy('sort_id', 'asc')
            ->get();

        return $this->formsPdfService->printProjectDocuments($project, $forms);
    }

    /**
     * @param SendEmailRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function send(SendEmailRequest $request)
    {
        $project = $this->project->findOrFail($request->get('project_id'));
        $forms = $this->projectForm
            ->where([
                'company_id' => auth()->user()->company_id,
                'project_id' => $project->id
            ])
            ->whereIn('form_id', $request->get('forms'))
            ->orderBy('sort_id', 'asc')
            ->get();

        $multipleFiles = $request->get('pdf_type') === 'multiple';
        $pdfs = $this->formsPdfService->printProjectDocuments($project, $forms, false, $multipleFiles);

        $recipients = $this->formsEmailService->getRecipients(
            $project->call_report,
            $request->get('recipients'),
            $request->get('custom_email')
        );

        $this->formsEmailService->sendForms($project, $pdfs, $recipients);

        return $this->respond(['message' => 'Email was successfully sent']);
    }
}
