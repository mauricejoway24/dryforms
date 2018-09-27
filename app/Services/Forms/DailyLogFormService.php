<?php

namespace App\Services\Forms;

use App\Models\ProjectDailylog;
use App\Models\StandardDailylogSetting;
use Illuminate\Support\Collection;

class DailyLogFormService
{
    /**
     * @var StandardDailylogSetting
     */
    private $standardDailyLogSettings;

    /**
     * @var ProjectDailylog
     */
    private $projectDailyLog;

    /**
     * DailyLogFormService constructor.
     * @param StandardDailylogSetting $standardDailyLogSetting
     * @param ProjectDailylog $projectDailyLog
     */
    public function __construct(StandardDailylogSetting $standardDailyLogSetting, ProjectDailylog $projectDailyLog)
    {
        $this->standardDailyLogSettings = $standardDailyLogSetting;
        $this->projectDailyLog = $projectDailyLog;
    }

    /**
     * @param int $projectId
     * @return Collection
     */
    public function getLogs(int $projectId)
    {
        $logs = $this->projectDailyLog->where([
            'project_id' => $projectId
        ]);
        $dailyLogSettings = $this->standardDailyLogSettings->first();
        if ($dailyLogSettings && !$dailyLogSettings->automatically_copy) {
            $logs = $logs->where('is_copied', 0);
        }

        return $logs->get();
    }
}
