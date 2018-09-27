<?php

namespace App\Services\Forms;

use App\Mail\FormsEmail;
use App\Models\Project;
use App\Models\ProjectCallReport;
use Illuminate\Mail\Mailer;

class FormsEmailService
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * FormsEmailService constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param Project $project
     * @param array $pdfs
     * @param array $recipients
     */
    public function sendForms(Project $project, array $pdfs, array $recipients)
    {
        $this->mailer->send(new FormsEmail($project, $pdfs, $recipients));
    }

    /**
     * @param ProjectCallReport $callReport
     * @param array $requestedRecipients
     * @param null|string $customEmail
     * @return array
     * @throws \Exception
     */
    public function getRecipients(ProjectCallReport $callReport, array $requestedRecipients, ?string $customEmail)
    {
        $emails = [];
        foreach ($requestedRecipients as $recipient) {
            $emails[] = $this->getEmail($callReport, $recipient, $customEmail);
        }
        return $emails;
    }

    /**
     * @param ProjectCallReport $callReport
     * @param string $recipient
     * @param null|string $customEmail
     * @return mixed|null|string
     * @throws \Exception
     */
    private function getEmail(ProjectCallReport $callReport, string $recipient, ?string $customEmail)
    {
        $email = null;

        switch ($recipient) {
            case 'self':
                $email = auth()->user()->email;
                break;
            case 'owner':
                $email = $callReport->insured_email;
                break;
            case 'insurance_adjuster':
                $email = $callReport->insurance_email;
                break;
            case 'manual':
                $email = $customEmail;
                break;
        }

        if (!$email) {
            throw new \Exception("$recipient email is not set");
        }

        return $email;
    }
}
