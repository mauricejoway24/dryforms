<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\ProjectInstrument;
use App\Models\ProjectMoistureForm;

class ProjectObserver
{
    /**
     * @var ProjectMoistureForm
     */
    private $projectMoistureForm;

    /**
     * @var ProjectInstrument
     */
    private $projectInstrument;

    /**
     * ProjectObserver constructor.
     * @param ProjectMoistureForm $projectMoistureForm
     * @param ProjectInstrument $projectInstrument
     */
    public function __construct(ProjectMoistureForm $projectMoistureForm, ProjectInstrument $projectInstrument)
    {
        $this->projectMoistureForm = $projectMoistureForm;
        $this->projectInstrument = $projectInstrument;
    }

    /**
     * @param Project $project
     */
    public function created(Project $project)
    {
        $instrument = $this->projectInstrument->where('company_id', $project->company_id)->orderBy('id', 'desc')->first();
        if (!$instrument) {
            return;
        }
        $this->projectInstrument->create([
            'make' => $instrument->make,
            'model' => $instrument->model,
            'project_id' => $project->id,
            'company_id' => $project->company_id
        ]);
        /** Not needed for now */
//        $this->projectMoistureForm->createDefault($project->id);
    }
}
