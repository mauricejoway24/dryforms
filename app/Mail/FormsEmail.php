<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormsEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var array
     */
    private $files;

    /**
     * @var array
     */
    private $recipients;

    /**
     * FormsEmail constructor.
     * @param Project $project
     * @param array $files
     * @param array $recipients
     */
    public function __construct(Project $project, array $files, array $recipients)
    {
        $this->project = $project;
        $this->files = $files;
        $this->recipients = $recipients;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->markdown('emails.forms')->subject('Project forms');

        foreach ($this->files as $file) {
            if(!$file) continue;
            $email->attach($file);
        }

        return $email->to($this->recipients);
    }
}
