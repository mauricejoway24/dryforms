<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FormOrders\FormOrdersStore;
use App\Models\ProjectForm;
use App\Models\StandardForm;
use App\Services\Projects\ProjectFormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SidebarController extends ApiController
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
     * @var ProjectFormService
     */
    private $projectFormService;

    /**
     * SidebarController constructor.
     * @param StandardForm $standardForm
     * @param ProjectForm $projectForm
     * @param ProjectFormService $projectFormService
     */
    public function __construct(StandardForm $standardForm, ProjectForm $projectForm, ProjectFormService $projectFormService)
    {
        $this->standardForm = $standardForm;
        $this->projectForm = $projectForm;
        $this->projectFormService = $projectFormService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->has('type') && $request->get('type') === 'project') {
            $forms = $this->projectForm
                ->where('project_id', $request->get('project_id'))
                ->orderBy('sort_id', 'asc')
                ->get();
        } else {
            $forms = $this->standardForm
                ->with('form')
                ->where('company_id', auth()->user()->company_id)
                ->orderBy('sort_id', 'asc')
                ->get();
            if ($forms->isEmpty()) $forms = $this->projectFormService->createStandardForms(auth()->user()->company);
        }

        return $this->respond($forms);
    }

    /**
     * @param FormOrdersStore $request
     *
     * @return JsonResponse
     */
    public function store(FormOrdersStore $request): JsonResponse
    {
        foreach ($request->get('forms') as $index => $formData) {
            $this->standardForm->where([
                'company_id' => auth()->user()->company_id,
                'form_id' => $formData['id']
            ])->update(['sort_id' => $index + 1]);
        }

        return $this->respond(['message' => 'Form Order successfully saved']);
    }
}
