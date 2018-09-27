<?php

namespace App\Services\Forms;

use App\Models\Project;
use App\Models\ProjectCallReport;
use App\Models\ProjectForm;
use App\Models\ProjectMoistureForm;
use App\Models\ProjectPsychometricDays;
use App\Services\Psychometric\PsychometricService;
use Barryvdh\Snappy\PdfWrapper;
use Illuminate\Support\Collection;
use LynX39\LaraPdfMerger\PdfManage;

class FormsPdfService
{
    /**
     * @var PdfWrapper
     */
    private $pdf;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var PsychometricService
     */
    private $psychometricService;

    /**
     * @var ScopeFormService
     */
    private $scopeFormService;

    /**
     * @var ProjectForm[]
     */
    private $forms;

    /**
     * @var string
     */
    private $baseStoragePath;

    /**
     * @var DailyLogFormService
     */
    private $dailyLogFormService;

    /**
     * FormsPdfService constructor.
     * @param PdfWrapper $pdf
     * @param PsychometricService $psychometricService
     * @param ScopeFormService $scopeFormService
     * @param DailyLogFormService $dailyLogFormService
     */
    public function __construct(
        PdfWrapper $pdf,
        PsychometricService $psychometricService,
        ScopeFormService $scopeFormService,
        DailyLogFormService $dailyLogFormService
    ) {
        $this->pdf = $pdf;
        $this->psychometricService = $psychometricService;
        $this->scopeFormService = $scopeFormService;
        $this->dailyLogFormService = $dailyLogFormService;
    }

    /**
     * @param Project $project
     * @param Collection $requestedForms
     * @param bool $download Whether service should return generated PDF or not
     * @param bool $multiple Whether service should merge PDFs into single or not
     * @return array|\LynX39\LaraPdfMerger\PDF|array
     * @throws \Exception
     */
    public function printProjectDocuments(Project $project, Collection $requestedForms, bool $download = true, bool $multiple = false)
    {
        $this->project = $project;
        $this->forms = $requestedForms;
        $this->baseStoragePath = "documents/pdf/{$project->company_id}";

        $pages = $this->createPdfForms();

        if (!$download) {
            if ($multiple) {
                return $pages;
            } else {
                return [public_path("{$this->baseStoragePath}/{$this->project->id}/documents.pdf")];
            }
        }

        $pdf = new PdfManage();
        foreach ($pages as $page) {
            if ($page) {
                $pdf->addPDF($page);
            }
        }

        return $pdf->merge('browser', public_path("{$this->baseStoragePath}/{$this->project->id}/documents.pdf"));
    }

    /**
     * @return array
     */
    private function createPdfForms(): array
    {
        $sections = [];

        foreach ($this->forms as $form) {
            $slug = $this->getFormSlug($form->standard_form->title);
            $sections[] = $this->getFormPdf($slug, $form);
        }

        return $sections;
    }

    /**
     * @param ProjectForm|null $form
     * @return array
     */
    private function getOptions(?ProjectForm $form)
    {
        return [
            'header-html' => $this->getHeader($form),
            'footer-html' => $this->getFooter($form),
            'margin-bottom' => $form ? '35mm' : '0mm',
            'margin-top' => $form ? '70mm' : '35mm',
            'margin-left' => '15mm',
            'margin-right' => '15mm',
            'enable-external-links' => true
        ];
    }

    /**
     * @param string $slug
     * @param ProjectForm $form
     * @return string
     */
    private function getFormPdf(string $slug, ProjectForm $form)
    {
        switch ($slug) {
            case 'call_report':
                return $this->generateCallReportPdf($this->project->call_report);
                break;
            case 'work_authorization':
            case 'anti_microbial':
            case 'release_from_liability':
            case 'work_stoppage':
            case 'certificate_of_completion':
                return $this->generateGenericFormPdf($form);
                break;
            case 'moisture_map':
                return $this->generateMoistureFormPdf($this->project->moisture_form, $form);
                break;
            case 'psychometric_report':
                return $this->generatePsychometricFormPdf($form);
                break;
            case 'project_scope':
                return $this->generateProjectScopePdf($form);
                break;
            case 'customer_responsibility':
                return $this->generateCustomerResponsibilityFormPdf($form);
                break;
            case 'daily_log':
                return $this->generateDailyLogPdf($form);
                break;
            default:
                return $this->generateGenericFormPdf($form);
                break;
        }
    }

    /**
     * @param ProjectCallReport $callReport
     * @return string
     */
    private function generateCallReportPdf(ProjectCallReport $callReport): string
    {
        $path = "$this->baseStoragePath/{$this->project->id}/test.pdf";

        $this->pdf->loadView('pdf.call_report', [
            'title' => 'Call Report',
            'callReport' => $callReport
        ])->setOptions($this->getOptions(null))
            ->setPaper('letter');

        $this->pdf->save($path, true);

        return $path;
    }

    /**
     * @param ProjectForm $form
     * @return string
     */
    private function generateGenericFormPdf(ProjectForm $form): string
    {
        $path = "{$this->baseStoragePath}/{$this->project->id}/{$form->title}.pdf";

        $this->pdf->loadView('pdf.generic_form', [
            'title' => $form->title,
            'form' => $form,
            'callReport' => $this->project->call_report
        ])->setOptions($this->getOptions($form))
            ->setPaper('letter');

        $this->pdf->save($path, true);

        return $path;
    }

    /**
     * @param ProjectMoistureForm $form
     * @param ProjectForm $projectForm
     * @return string
     */
    private function generateMoistureFormPdf(ProjectMoistureForm $form, ProjectForm $projectForm): string
    {
        $path = "{$this->baseStoragePath}/{$this->project->id}/{$projectForm->title}.pdf";

        $this->pdf->loadView('pdf.moisture_form', [
            'title' => $projectForm->title,
            'form' => $form,
            'callReport' => $this->project->call_report
        ])->setOptions($this->getOptions($projectForm))
            ->setPaper('letter');

        $this->pdf->save($path, true);

        return $path;
    }

    /**
     * @param ProjectForm $projectForm
     * @return string
     */
    private function generateProjectScopePdf(ProjectForm $projectForm)
    {
        $path = "{$this->baseStoragePath}/{$this->project->id}/{$projectForm->title}.pdf";

        foreach ($projectForm->project->areas as $area) {
            $area->scopes = $this->scopeFormService->getAreaScopes($this->project->id, $area->id);
        }

        $miscScopes = $this->scopeFormService->getMiscScopes($this->project->id);

        $this->pdf->loadView('pdf.project_scope', [
            'title' => $projectForm->title,
            'form' => $projectForm,
            'callReport' => $this->project->call_report,
            'miscScopes' => $miscScopes
        ])->setOptions($this->getOptions($projectForm))
            ->setPaper('letter');

        $this->pdf->save($path, true);

        return $path;
    }

    /**
     * @param ProjectForm $form
     * @return string
     */
    private function generatePsychometricFormPdf(ProjectForm $form): string
    {
        $path = "{$this->baseStoragePath}/{$this->project->id}/{$form->title}.pdf";
        $form->measurements = $this->psychometricService->prepareMeasurements($this->project, new ProjectPsychometricDays());

        $this->pdf->loadView('pdf.psychometric_form', [
            'title' => $form->title,
            'form' => $form,
            'callReport' => $this->project->call_report
        ])->setOptions($this->getOptions($form))
            ->setPaper('letter');

        $this->pdf->save($path, true);

        return $path;
    }

    /**
     * @param ProjectForm $form
     * @return string
     */
    private function generateCustomerResponsibilityFormPdf(ProjectForm $form): string
    {
        $path = "{$this->baseStoragePath}/{$this->project->id}/{$form->title}.pdf";

        $this->pdf->loadView('pdf.responsibility_form', [
            'title' => $form->title,
            'form' => $form,
            'callReport' => $this->project->call_report
        ])->setOptions($this->getOptions($form))
            ->setPaper('letter');

        $this->pdf->save($path, true);

        return $path;
    }

    /**
     * @param ProjectForm $form
     * @return string
     */
    private function generateDailyLogPdf(ProjectForm $form): string
    {
        $path = "{$this->baseStoragePath}/{$this->project->id}/{$form->title}.pdf";
        $logs = $this->dailyLogFormService->getLogs($form->project_id);

        $this->pdf->loadView('pdf.daily_log', [
            'title' => $form->title,
            'form' => $form,
            'logs' => $logs,
            'callReport' => $this->project->call_report
        ])->setOptions($this->getOptions($form))
            ->setPaper('letter');

        $this->pdf->save($path, true);

        return $path;
    }

    /**
     * @param ProjectForm|null $form
     * @return string
     */
    private function getHeader(?ProjectForm $form)
    {
        return view()->make('pdf.layout._header', [
            'company' => $this->project->company_details,
            'form' => $form,
            'title' => $form ? $form->title : null,
            'callReport' => $this->project->call_report
        ])->render();
    }

    /**
     * @param ProjectForm|null $form
     * @return string
     */
    private function getFooter(?ProjectForm $form)
    {
        return view()->make('pdf.layout._footer', ['form' => $form, 'callReport' => $this->project->call_report])->render();
    }

    /**
     * @param string $title
     * @return string
     */
    private function getFormSlug(string $title): string
    {
        return snake_case(str_replace('-', ' ', $title));
    }
}
