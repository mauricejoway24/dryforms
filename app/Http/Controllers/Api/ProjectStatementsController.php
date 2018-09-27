<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProjectStatement\ProjectStatementRevert;
use App\Models\DefaultStatement;
use App\Models\StandardStatement;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectStatement\ProjectStatementUpdate;
use App\Models\ProjectStatement;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectStatementsController extends ApiController
{
    /**
     * @var ProjectStatement
     */
    private $projectStatement;

    /**
     * @var DefaultStatement
     */
    private $defaultStatement;

    /**
     * @var StandardStatement
     */
    private $standardStatement;

    /**
     * ProjectStatementsController constructor.
     *
     * @param ProjectStatement $projectStatement
     * @param DefaultStatement $defaultStatement
     * @param StandardStatement $standardStatement
     */
    public function __construct(
        ProjectStatement $projectStatement,
        DefaultStatement $defaultStatement,
        StandardStatement $standardStatement
    ) {
        $this->projectStatement = $projectStatement;
        $this->defaultStatement = $defaultStatement;
        $this->standardStatement = $standardStatement;
    }

    /**
     * @param ProjectStatementUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectStatementUpdate $request): JsonResponse
    {
    	$queryParams = $request->validatedOnly();
       	$projectStatement = $this->projectStatement
            ->findOrFail($queryParams['id']);
        unset($queryParams['id']);
        $projectStatement->update($queryParams);

        return $this->respond(['message' => 'Project Statement successfully updated', 'projectStatement' => $projectStatement]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setTitleAsSelected(Request $request): JsonResponse
    {
        $checkedStatements = $this->projectStatement
            ->where('form_id', $request->get('form_id'))
            ->where('project_id', $request->get('project_id'))
            ->get();

        foreach ($checkedStatements as $statement) {
            if ($statement->id === $request->get('selected_id')) {
                $statement->checked = true;
            } else {
                $statement->checked = false;
            }
            $statement->save();
        }

        return $this->respond([
            'message' => 'Project Statement successfully updated',
            'id' => $request->get('selected_id')
        ]);
    }

    /**
     * @param int $statementId
     * @param ProjectStatementRevert $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertStatement(int $statementId, ProjectStatementRevert $request)
    {
        $defaultStatement = $this->defaultStatement->where('form_id', $request->get('form_id'))->firstOrFail();
        $standardStatement = $this->standardStatement->findOrFail($statementId);
        $standardStatement->statement = $defaultStatement->statement;
        $standardStatement->save();

        return $this->respond([
            'message' => 'Project Statement successfully reverted',
        ]);
    }
}
