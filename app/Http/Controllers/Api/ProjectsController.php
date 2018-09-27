<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Projects\ProjectsIndex;
use App\Http\Requests\Projects\ProjectStore;
use App\Http\Requests\Projects\ProjectUpdate;
use App\Models\ProjectCompanyDetails;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use App\Services\QueryBuilders\ProjectModelQueryBuilder;
use App\Models\Project;
use App\Models\ProjectCallReport;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectsController extends ApiController
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var ProjectCallReport
     */
    private $projectCallReport;

    /**
     * @var ProjectCompanyDetails
     */
    private $projectCompanyDetails;

    /**
     * ProjectsController constructor.
     * @param Project $project
     * @param ProjectCallReport $projectCallReport
     * @param ProjectCompanyDetails $projectCompanyDetails
     */
    public function __construct(Project $project, ProjectCallReport $projectCallReport, ProjectCompanyDetails $projectCompanyDetails)
    {
        $this->project = $project;
        $this->projectCallReport = $projectCallReport;
        $this->projectCompanyDetails = $projectCompanyDetails;
    }

    /**
     * @param ProjectsIndex $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProjectsIndex $request): JsonResponse
    {
        $queryParams = $request->validatedOnly();
        $queryBuilder = new ProjectModelQueryBuilder();
        $projects = $queryBuilder
            ->setQuery($this->project->query())
            ->setQueryParams($queryParams);

        $projects = $projects->paginate($request->get('per_page'));

        return $this->respond($projects);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $project = $this->project->findOrFail($id);

        return $this->respond($project);
    }

    /**
     * @param ProjectStore $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProjectStore $request)
    {
        $project = $this->project->create($request->validatedOnly());
        $this->projectCompanyDetails->create([
            'logo' => $project->company->logo,
            'name' => $project->company->name,
            'email' => $project->company->email,
            'street' => $project->company->street,
            'city' => $project->company->city,
            'state' => $project->company->state,
            'zip' => $project->company->zip,
            'phone' => $project->company->phone,
            'project_id' => $project->id
        ]);

        return $this->respond(['message' => 'Project successfully created', 'project' => $project]);
    }

    /**
     * @param ProjectUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectUpdate $request)
    {
        $project = $this->project->findOrFail($request->input('project_id'));
        $project->update($request->validatedOnly());

        return $this->respond(['message' => 'Project successfully updated', 'project' => $project]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreStatus(Request $request)
    {
        $projectId = $request->input('project_id');
        
        $projectCallReport = $this->projectCallReport
            ->where('project_id', $projectId)
            ->first();
        $status = ProjectStatus::IN_PROGRESS;
        if ($projectCallReport && $projectCallReport->date_completed) {
            $status = ProjectStatus::COMPLETED;
        }

        $project = $this->project->findOrFail($projectId);
        $project->update([
            'status' => $status
        ]);

        return $this->respond(['message' => 'Project successfully updated', 'project' => $project]);
    }
    
    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $project = $this->project->findOrFail($id);
        $project->update([
            'status' => ProjectStatus::DELETED
        ]);

        return $this->respond(['message' => 'Project successfully deleted']);
    }

    /**TODO what is this?
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompany(Request $request)
    {
        $project = $this->project->findOrFail($request->input('project_id'));

        return $this->respond($project->company);
    }
}