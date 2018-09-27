<?php
namespace App\Http\Controllers\Api;


use App\Http\Requests\ProjectMoisture\AddDatesRequest;
use App\Http\Requests\ProjectMoisture\ProjectMoistureDateUpdate;
use App\Http\Requests\ProjectMoisture\ProjectMoistureUpdate;
use App\Models\Project;
use App\Models\ProjectMoistureDay;
use App\Models\ProjectMoistureDayData;
use App\Models\ProjectMoistureForm;
use Carbon\Carbon;

class ProjectMoistureController extends ApiController
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var ProjectMoistureForm
     */
    private $moistureForm;

    /**
     * @var ProjectMoistureDay
     */
    private $projectMoistureDay;

    /**
     * @var ProjectMoistureDayData
     */
    private $projectMoistureDayData;

    /**
     * ProjectMoistureController constructor.
     * @param Project $project
     * @param ProjectMoistureForm $moistureForm
     * @param ProjectMoistureDay $projectMoistureDay
     * @param ProjectMoistureDayData $projectMoistureDayData
     */
    public function __construct(
        Project $project,
        ProjectMoistureForm $moistureForm,
        ProjectMoistureDay $projectMoistureDay,
        ProjectMoistureDayData $projectMoistureDayData
    ) {
        $this->project = $project;
        $this->moistureForm = $moistureForm;
        $this->projectMoistureDay = $projectMoistureDay;
        $this->projectMoistureDayData = $projectMoistureDayData;
    }

    /**
     * @param int $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $projectId)
    {
        $project = $this->project->findOrFail($projectId);
        $moistureForm = $project->moisture_form;

        return $this->respond([
            'project' => $project,
            'moisture_form' => $moistureForm
        ]);
    }

    /**
     * @param AddDatesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addDates(AddDatesRequest $request)
    {
        $project = $this->project->findOrFail($request->get('project_id'));
        $areas = $request->get('selected_areas');
        $dateRange = $request->get('date_range');
        $dateRange = explode(' ~ ', $dateRange);
        $fromDate = new Carbon($dateRange[0]);
        $toDate = new Carbon($dateRange[1]);
        $difference = $fromDate->diff($toDate)->days;

        for ($i = 0; $i <= $difference; $i++) {
            $projectMoistureDay = $this->projectMoistureDay->where([
                'date' => $fromDate->format('Y-m-d'),
                'moisture_form_id' => $project->moisture_form->id
            ])->first();
            if (!$projectMoistureDay) {
                $projectMoistureDay = $this->projectMoistureDay->create([
                    'moisture_form_id' => $project->moisture_form->id,
                    'date' => $fromDate->format('Y-m-d'),
                ]);
            }
            foreach ($areas as $areaId) {
                $dayData = $this->projectMoistureDayData->where([
                    'area_id' => $areaId,
                    'day_id' => $projectMoistureDay->id
                ])->first();
                if (!$dayData) {
                    $this->projectMoistureDayData->createDefault($projectMoistureDay->id, $areaId);

                }
            }
            $fromDate->addDay();
        }

        return $this->respond(['message' => 'Form successfully saved']);
    }

    /**
     * @param ProjectMoistureUpdate $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectMoistureUpdate $request)
    {
        $dayData = $this->projectMoistureDayData->findOrFail($request->get('id'));
        $dayData->data = $request->get('data');
        $dayData->save();

        return $this->respond([
            'message' => 'Form successfully updated'
        ]);
    }

    /**
     * @param int $id
     * @param ProjectMoistureDateUpdate $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDate(int $id, ProjectMoistureDateUpdate $request)
    {
        $day = $this->projectMoistureDay->findOrFail($id);
        $day->date = $request->get('date');
        $day->save();

        return $this->respond([
            'message' => 'Form successfully updated'
        ]);
    }

    /**
     * @param int $areaDataId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $areaDataId)
    {
        $dayData = $this->projectMoistureDayData->findOrFail($areaDataId);
        $dayData->delete();

        return $this->respond([
            'message' => 'Form successfully updated'
        ]);
    }
}
