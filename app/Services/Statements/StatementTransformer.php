<?php

namespace App\Services\Statements;

use App\Models\ProjectCallReport;

class StatementTransformer
{
    /**
     * @param ProjectCallReport $callReport
     * @param string $statement
     * @return string
     */
    public static function transformForPdf(ProjectCallReport $callReport, string $statement): string
    {
        $transformer = new StatementTransformer();

        return $transformer->replaceTokens($callReport, $statement);
    }

    /**
     * @param int $projectId
     * @param string $statement
     * @return string
     */
    public function transform(int $projectId, string $statement): string
    {
        $callReport = $this->getCallReport($projectId);

        return $this->replaceTokens($callReport, $statement);
    }

    /**
     * @param ProjectCallReport $callReport
     * @param string $statement
     * @return string
     */
    private function replaceTokens(ProjectCallReport $callReport, string $statement)
    {
        $tokens = $this->getTokensMap();

        foreach ($tokens as $token => $replacer) {
            $statement = preg_replace_array($token, [$callReport->{$replacer}], $statement);
        }

        return $statement;
    }

    /**
     * @param int $projectId
     * @return ProjectCallReport
     */
    private function getCallReport(int $projectId): ProjectCallReport
    {
        return ProjectCallReport::where('project_id', $projectId)->first() ?? new ProjectCallReport();
    }

    private function getTokensMap(): array
    {
        return [
            '/%apartment #%/' => 'apartment_no',
            '/%apartment name%/' => 'apartment_name',
            '/%assigned to%/' => 'assignee_name',
            '/%building #%/' => 'building_no',
            '/%category%/' => 'category',
            '/%claim #%/' => 'insurance_claim_no',
            '/%class%/' => 'class',
            '/%company%/' => 'company_details_name',
            '/%company phone%/' => 'company_details_phone',
            '/%contact name%/' => 'contact_name',
            '/%cross streets%/' => 'cross_streets',
            '/%date completed%/' => 'formatted_date_completed',
            '/%date contacted%/' => 'formatted_date_contacted',
            '/%date of loss%/' => 'formatted_date_loss',
            '/%deductible%/' => 'insurance_deductible',
            '/%gate code%/' => 'gate_code',
            '/%insurance adjuster%/' => 'insurance_adjuster',
            '/%insurance company%/' => 'insurance_company',
            '/%job address%/' => 'job_address',
            '/%owner\/insured name%/' => 'insured_name',
            '/%policy #%/' => 'insurance_policy_no',
            '/%point of loss%/' => 'point_loss',
            '/%time contacted%/' => 'time_contacted'
        ];
    }
}
