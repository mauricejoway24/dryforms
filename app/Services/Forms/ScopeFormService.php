<?php
namespace App\Services\Forms;

use App\Models\ProjectScope;

class ScopeFormService
{
    /**
     * @var ProjectScope
     */
    private $scope;

    /**
     * ScopeFormService constructor.
     * @param ProjectScope $scope
     */
    public function __construct(ProjectScope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * @param int $projectId
     * @param int $areaId
     * @return mixed
     */
    public function getAreaScopes(int $projectId, int $areaId)
    {
        return $this->scope->where([
            'project_id' => $projectId,
            'project_area_id' => $areaId
        ])->get();
    }

    /**
     * @param int $projectId
     * @return mixed
     */
    public function getMiscScopes(int $projectId)
    {
        return $this->scope->where([
            'project_id' => $projectId,
            'project_area_id' => null
        ])->get();
    }
}
