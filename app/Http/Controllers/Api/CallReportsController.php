<?php

namespace App\Http\Controllers\Api;

use App\Models\ProjectCallReport;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ReviewLink;
use App\Models\ReviewRequestMessage;
use Illuminate\Http\JsonResponse;
use App\Mail\AppMailer;

use App\Http\Requests\ProjectCallReport\ProjectCallReportIndex;
use App\Http\Requests\ProjectCallReport\ProjectCallReportStore;
use App\Http\Requests\ProjectCallReport\ProjectCallReportUpdate;

class CallReportsController extends ApiController
{
    /**
     * @var ProjectCallReport
     */
    private $projectCallReport;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var ReviewLink
     */
    private $reviewLink;

    /**
     * @var ReviewRequestMessage
     */
    private $reviewRequestMessage;
    /**
     * ProjectCallReportsController constructor.
     *
     * @param ProjectCallReport $projectCallReport
     * @param Project $project
     * @param ReviewLink $reviewLink
     * @param ReviewRequestMessage $reviewRequestMessage
     */
    public function __construct(ProjectCallReport $projectCallReport, Project $project, ReviewLink $reviewLink, ReviewRequestMessage $reviewRequestMessage)
    {
        $this->projectCallReport = $projectCallReport;
        $this->project = $project;
        $this->reviewLink = $reviewLink;
        $this->reviewRequestMessage = $reviewRequestMessage;
    }

    /**
     * @param ProjectCallReportIndex $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProjectCallReportIndex $request): JsonResponse
    {
        $project = $this->project->findOrFail($request->route('project'));
        $projectCallReport = $this->projectCallReport
        	->where('project_id', $request->route('project'))
        	->first();

        return $this->respond([
            'call_report' => $projectCallReport ?? new ProjectCallReport() ,
            'project' => $project
        ]);
    }

     /**
     * @param ProjectCallReportStore $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProjectCallReportStore $request)
    {
        $projectCallReport = $this->projectCallReport->create($request->validatedOnly());

        return $this->respond([
            'message' => 'Project Call Report successfully created',
            'call_report' => $projectCallReport
        ]);
    }

    /**
     * @param ProjectCallReportUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProjectCallReportUpdate $request, AppMailer $mailer)
    {
        $projectCallReport = $this->projectCallReport->findOrFail($request->input('call_report_id'));
        $projectCallReport->update($request->validatedOnly());

        $project = $this->project->findOrFail($request->get('project_id'));
        $project->update([
            'owner_name' => $request->get('insured_name'),
            'address' => $request->get('job_address'),
            'phone' => $request->get('insured_cell_phone') ? $request->get('insured_cell_phone') : ($request->get('insured_home_phone') ? $request->get('insured_home_phone') : ($request->get('insured_work_phone') ? $request->get('insured_work_phone'): '')),
            'status' => $request->get('date_completed') ? ProjectStatus::COMPLETED : ProjectStatus::IN_PROGRESS,
            'assigned_to' => $request->get('assigned_to')
        ]);

        if ($request->get('insured_email') && $request->get('date_completed') && $this->reviewLink->where('activate', 1)->exists()) {
            $reviewLinks = $this->reviewLink->where('activate', 1)->get();
            if ($this->reviewRequestMessage->count() == 0) {
                $this->reviewRequestMessage->create([
                    'company_id' => auth()->user()->company_id,
                    'message' => "We appreciate your business and hope we did a good job. <br> If you don't mind, could you please lease us a review at 1 or all of the sites below as we're trying to build our online reputation <br> <br> <a href='%1'>%1</a> <br> <a href='%2'>%2</a> <br> <a href='%3'>%3</a> <br>"
                ]);
            }
            $reviewRequestMessage = $this->reviewRequestMessage->first()->message;
            foreach($reviewLinks as $key => $reviewLink) {
                $reviewRequestMessage = str_replace("%". ($key+1), $reviewLink['url'], $reviewRequestMessage);
            }
            $reviewRequestMessage = preg_replace('/%\d*/', '', $reviewRequestMessage);
            $mailer->sendReviewRequest($request->get('insured_email'), $reviewRequestMessage);
        }

        return $this->respond([
            'message' => 'Project Call Report successfully updated',
            'call_report' => $projectCallReport
        ]);
    }

}
