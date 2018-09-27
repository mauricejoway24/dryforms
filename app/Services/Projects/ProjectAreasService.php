<?php

namespace App\Services\Projects;

use App\Models\ProjectArea;
use App\Models\ProjectMoistureDay;
use App\Models\ProjectMoistureDayData;
use App\Models\ProjectPsychometricDays;

class ProjectAreasService
{
    /**
     * @var ProjectMoistureDay
     */
    private $projectMoistureDay;
    /**
     * @var ProjectMoistureDayData
     */
    private $projectMoistureDayData;

    /**
     * @var ProjectPsychometricDays
     */
    private $projectPsychometricDays;

    /**
     * ProjectAreasService constructor.
     * @param ProjectMoistureDay $projectMoistureDay
     * @param ProjectMoistureDayData $projectMoistureDayData
     * @param ProjectPsychometricDays $projectPsychometricDays
     */
    public function __construct(
        ProjectMoistureDay $projectMoistureDay,
        ProjectMoistureDayData $projectMoistureDayData,
        ProjectPsychometricDays $projectPsychometricDays
    ) {

        $this->projectMoistureDay = $projectMoistureDay;
        $this->projectMoistureDayData = $projectMoistureDayData;
        $this->projectPsychometricDays = $projectPsychometricDays;
    }

    /**
     * @param ProjectArea $area
     * @param string $date
     */
    public function addMoistureDays(ProjectArea $area, string $date)
    {
        if ($area->project->moisture_form) {
            $projectMoistureDay = $this->projectMoistureDay->where([
                'date' => $date,
                'moisture_form_id' => $area->project->moisture_form->id
            ])->first();
            if (!$projectMoistureDay) {
                $projectMoistureDay = $this->projectMoistureDay->create([
                    'moisture_form_id' => $area->project->moisture_form->id,
                    'date' => date('Y-m-d'),
                ]);
            }
            $this->projectMoistureDayData->createDefault($projectMoistureDay->id, $area->id);
        }
    }

    /**
     * @param ProjectArea $area
     */
    public function addMeasurementsForNewArea(ProjectArea $area)
    {
        $this->addMoistureMeasurements($area);
        $this->addPsychometricMeasurements($area);
    }

    /**
     * @param ProjectArea $area
     */
    private function addMoistureMeasurements(ProjectArea $area)
    {
        foreach ($area->project->moisture_form->days as $day) {
            $existing = $this->projectMoistureDayData->where([
                'day_id' => $day->id,
                'area_id' => $area->id
            ])->first();
            if (!$existing) {
                $this->projectMoistureDayData->createDefault($day->id, $area->id);
            }
        }
    }

    /**
     * @param ProjectArea $projectArea
     */
    private function addPsychometricMeasurements(ProjectArea $projectArea)
    {
        $existingDates = [];
        foreach ($projectArea->project->areas as $area) {
            foreach ($area->psychometric_days as $day) {
                $existingDates[] = $day->current_time;
            }
        }

        foreach ($existingDates as $date) {
            $existing = $this->projectPsychometricDays->where([
                'area_id' => $projectArea->id,
                'current_time' => $date
            ])->first();
            if (!$existing) {
                $this->projectPsychometricDays->createDefault($projectArea->id, $date);
            }
        }
    }
}
