<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Psychometric\StoreRequest;
use App\Http\Requests\Psychometric\UpdateMeasurementRequest;
use App\Http\Requests\Psychometric\UpdateRequest;
use App\Http\Requests\Psychometric\UpdateTimeRequest;
use App\Models\Project;
use App\Models\ProjectPsychometricDays;
use App\Services\Psychometric\PsychometricService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProjectPsychometricController extends ApiController
{
    /**
     * @var ProjectPsychometricDays
     */
    private $projectPsychometricDays;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var Carbon
     */
    private $carbon;

    /**
     * @var PsychometricService
     */
    private $psychometricService;

    /**
     * ProjectPsychometricController constructor.
     * @param ProjectPsychometricDays $projectPsychometricDays
     * @param Project $project
     * @param Carbon $carbon
     * @param PsychometricService $psychometricService
     */
    public function __construct(ProjectPsychometricDays $projectPsychometricDays, Project $project, Carbon $carbon, PsychometricService $psychometricService)
    {
        $this->projectPsychometricDays = $projectPsychometricDays;
        $this->project = $project;
        $this->carbon = $carbon;
        $this->psychometricService = $psychometricService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $project = $this->project->with(['areas'])->findOrFail($request->segment(4));

        $measurements = $this->psychometricService->prepareMeasurements($project, $this->projectPsychometricDays);

        return $this->respond([
            'project' => $project,
            'measurements' => $measurements
        ]);
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $areas = $request->get('selected_areas');
        $dateRange = $request->get('date_range');
        $dateRange = explode(' ~ ', $dateRange);
        $fromDate = new Carbon($dateRange[0]);
        $toDate = new Carbon($dateRange[1]);

        $difference = $fromDate->diff($toDate)->days;

        for ($i = 0; $i <= $difference; $i++) {
            foreach ($areas as $areaId) {
                if (!$areaId) continue;
                $existingRecord = $this->projectPsychometricDays->where([
                    'area_id' => $areaId,
                    'current_time' => $fromDate->format('Y-m-d')
                ])->first();
                if ($existingRecord) continue;
                $containmentsQuantity = $this->projectPsychometricDays->where(['area_id' => $areaId])->groupBy('containment_id')->get(['containment_id'])->count();
                if ($containmentsQuantity > 1) {
                    /** If same area has containments we should add them to ne days also */
                    for ($i = 0; $i < $containmentsQuantity; $i++) {
                        $this->projectPsychometricDays->createDefault($areaId, $fromDate->format('Y-m-d'), $i + 1);
                    }
                } else {
                    $this->projectPsychometricDays->createDefault($areaId, $fromDate->format('Y-m-d'));
                }
            }
            $fromDate->addDay();
        }

        return $this->respond(['message' => 'Form successfully saved']);
    }

    /**
     * @param UpdateMeasurementRequest $request
     * @return JsonResponse
     */
    public function updateMeasurements(UpdateMeasurementRequest $request)
    {
        $day = $this->projectPsychometricDays->findOrFail($request->route('id'));
        $day->update([
            'outside' => $request->get('outside'),
            'unaffected' => $request->get('unaffected'),
            'affected' => $request->get('affected'),
            'dehumidifier' => $request->get('dehumidifier'),
            'hvac' => $request->get('hvac')
        ]);

        if ($request->get('update_all_measurements')) {
            $project = $this->project->with(['areas'])->findOrFail($request->get('project_id'));
            $this->psychometricService->duplicateMeasurements($project->areas, $day);
        }

        return $this->respond([
            'message' => 'Form successfully saved'
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param int $areaId
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $areaId): JsonResponse
    {
        $psychometricDays = $this->projectPsychometricDays
            ->where('area_id', $areaId)
            ->orderBy('containment_id', 'asc')
            ->get();
        $currentContainment = $psychometricDays->last()->containment_id ? $psychometricDays->last()->containment_id : 0;
        $contaminantsQuantity = $request->get('contaminants_quantity');

        $startFromContainment = $currentContainment ? $currentContainment + 1 : 1;
        $contaminantsQuantity = $currentContainment ? $currentContainment + $contaminantsQuantity : $contaminantsQuantity;
        foreach ($psychometricDays as $day) {
//            dd($startFromContainment);
//            dd($contaminantsQuantity);
            for ($i = $startFromContainment; $i <= $contaminantsQuantity; $i++) {
                $existing = $this->projectPsychometricDays->where([
                    'area_id' => $day->area_id,
                    'containment_id' => $i,
                    'current_time' => $day->current_time,
                ])->first();
                if (!$existing) {
                    $this->projectPsychometricDays->create([
                        'area_id' => $day->area_id,
                        'containment_id' => $i,
                        'current_time' => $day->current_time,
                        'outside' => $day->outside,
                        'unaffected' => $day->unaffected,
                        'affected' => $i === 1 ? $day->affected : ProjectPsychometricDays::DEFAULT_MEASUREMENTS,
                        'dehumidifier' => $i === 1 ? $day->dehumidifier : ProjectPsychometricDays::DEFAULT_MEASUREMENTS,
                        'hvac' => $i === 1 ? $day->hvac : ProjectPsychometricDays::DEFAULT_MEASUREMENTS
                    ]);
                }
            }
            if (!$currentContainment) {
                $day->delete();
            }
        }

        return $this->respond(['message' => 'Form successfully updated']);
    }

    /**
     * @param UpdateTimeRequest $request
     * @return JsonResponse
     */
    public function updateTime(UpdateTimeRequest $request): JsonResponse
    {
        $currentTime = $request->get('old_time');
        $newTime = $request->get('new_time');
        $project = $this->project->findOrFail($request->get('project_id'));

        foreach ($project->areas as $area) {
            $area->psychometric_days()->where([
                'current_time' => $currentTime
            ])->update(['current_time' => $newTime]);
        }

        return $this->respond(['message' => 'Form successfully updated']);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->projectPsychometricDays->findOrFail($id)->delete();

        return $this->respond(['message' => 'Area successfully deleted']);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroyDay(int $id)
    {
        $psychometricDay = $this->projectPsychometricDays->findOrFail($id);
        $psychometricDay->where([
            'area_id' => $psychometricDay->area_id,
            'current_time' => $psychometricDay->current_time
        ])->delete();

        return $this->respond(['message' => 'Day successfully deleted']);
    }
}
