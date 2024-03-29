<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;

class DryFormsPlus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $project_id;
    public $pdf_flag;
    public $allforms;

    public function __construct($projectId, $pdfFlag, $allForms)
    {
        $this->project_id = $projectId;
        $this->pdf_flag = $pdfFlag;
        $this->allforms = $allForms;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $allforms = $this->allforms;
        $projectid = $this->project_id;
        $pdfflag = $this->pdf_flag;
        $msg = $this->view('sendView')->subject('DryFormsPlus');

        $storagePath = storage_path('app/public/forms');
        $pdfPath = ($storagePath . '/pdf');
        if($pdfflag == 1){
            foreach($allforms as $key => $value){
                $msg->attach($pdfPath . "/dryforms_project$projectid-$value.pdf");    
            }
        }
        else{
            return $msg->attach($pdfPath . "/dryforms_project$projectid.pdf");
        }
        return $msg;
    }
}
