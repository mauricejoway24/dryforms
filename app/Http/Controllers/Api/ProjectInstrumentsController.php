<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectInstrument;
use Illuminate\Http\Request;

class ProjectInstrumentsController extends ApiController
{
    /**
     * @var Project
     */
    private $project;

    /**
     * ProjectInstrumentsController constructor.
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @param Request $request
     */
    public function update(Request $request)
    {
        $project = $this->project->findOrFail($request->segment(4));
        $projectInstrument = $project->instrument ?? new ProjectInstrument();
        $projectInstrument->make = $request->has('make') ? $request->get('make') : $projectInstrument->make;
        $projectInstrument->model = $request->has('model') ? $request->get('model') : $projectInstrument->model;
        $projectInstrument->project_id = $project->id;
        $projectInstrument->company_id = $project->company_id;
        $projectInstrument->save();
    }
}
