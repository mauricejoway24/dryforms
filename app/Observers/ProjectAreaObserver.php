<?php

namespace App\Observers;

use App\Models\ProjectArea;
use App\Models\ProjectMoistureDay;
use App\Models\ProjectMoistureDayData;
use App\Services\Projects\ProjectAreasService;

class ProjectAreaObserver
{
    /**
     * @var ProjectAreasService
     */
    private $projectAreasService;

    /**
     * ProjectAreaObserver constructor.
     * @param ProjectAreasService $projectAreasService
     */
    public function __construct(ProjectAreasService $projectAreasService)
    {
        $this->projectAreasService = $projectAreasService;
    }

    /**
     * @param ProjectArea $area
     */
    public function created(ProjectArea $area)
    {
        $date = date('Y-m-d');
        $projectMoistureDay = null;

        $this->projectAreasService->addMoistureDays($area, $date);
        $this->projectAreasService->addMeasurementsForNewArea($area);
    }

    /**
     * @param ProjectArea $area
     */
    public function deleted(ProjectArea $area)
    {
        //
    }
}
