<?php

namespace App\Services\Psychometric;

use App\Models\Project;
use App\Models\ProjectPsychometricDays;
use Illuminate\Support\Collection;

class PsychometricService
{
    /**
     * @param Collection $areas
     * @param ProjectPsychometricDays $day
     */
    public function duplicateMeasurements(Collection $areas, ProjectPsychometricDays $day)
    {
        foreach ($areas as $area) {
            $days = $area->psychometric_days()->where('current_time', $day->current_time)->where('id', '!=', $day->id)->get();
            foreach($days as $dayToUpdate) {
                $dayToUpdate->outside = $day->outside;
                $dayToUpdate->unaffected = $day->unaffected;
                $dayToUpdate->save();
            }
        }
    }

    /**
     * @param Project $project
     * @param ProjectPsychometricDays $projectPsychometricDays
     * @return array
     */
    public function prepareMeasurements(Project $project, ProjectPsychometricDays $projectPsychometricDays)
    {
        $measurements = [];

        foreach ($project->areas as $key => $area) {
            /** @var Collection $days $days */
            $days = $area->psychometric_days()->orderBy('current_time', 'asc')->get();
            foreach ($days as $day) {
                $measurements[$day->current_time]['date'] = $day->current_time;
                $measurements[$day->current_time]['areas'][$area->id]['title'] = $area->standard_area->title;
                $measurements[$day->current_time]['areas'][$area->id]['measurements'][] = $day;
            }
            if ($days->isEmpty()) {
                $day = $projectPsychometricDays->createDefault($area->id);
                $measurements[$day->current_time]['date'] = $day->current_time;
                $measurements[$day->current_time]['areas'][$area->id]['title'] = $area->standard_area->title;
                $measurements[$day->current_time]['areas'][$area->id]['measurements'][] = $day;
            }
        }

        return $measurements;
    }
}
