<?php
namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Contracts\Mail\Mailer;

class AppMailer {
    protected $mailer; 
    public $fromAddress = '';
    protected $fromName = 'DryFormsPlus';
    protected $to;
    protected $subject;
    protected $view;
    protected $data = [];

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->fromAddress = env('MAIL_FROM_ADDRESS', 'john@dryformsplus.com');
    }

    public function sendTicketInformation($user, Ticket $ticket)
    {
        $this->to = $user->email;
        $this->subject = "[Ticket ID: $ticket->ticket_id] $ticket->title";
        $this->view = 'emails.ticket_info';
        $this->data = compact('user', 'ticket');

        return $this->deliver();
    }

    public function sendTicketComments($ticketOwner, $user, Ticket $ticket, $comment)
    {
        $this->to = $ticketOwner->email;
        $this->subject = "RE: $ticket->title (Ticket ID: $ticket->ticket_id)";
        $this->view = 'emails.ticket_comments';
        $this->data = compact('ticketOwner', 'user', 'ticket', 'comment');

        return $this->deliver();
    }

    public function sendTicketStatusNotification($ticketOwner, Ticket $ticket)
    {
        $this->to = $ticketOwner->email;
        $this->subject = "RE: $ticket->title (Ticket ID: $ticket->ticket_id)";
        $this->view = 'emails.ticket_status';
        $this->data = compact('ticketOwner', 'ticket');

        return $this->deliver();
    }

    // public function sendInvitation($fullName, $email, $password, $code, $expiration)
    public function sendInvitation($fullName, $email, $password, $expiration)
    {
        $this->to = $email;
        // $this->subject = "Invitation from DryformsPlus (code: ". $code['code']. ")";
        $this->subject = "Invitation from DryformsPlus";
        $this->view = 'emails.invitation';
        $this->data = [
            'fullName' => $fullName,
            // 'code' => $code,
            'encryptEmail' => encrypt($email, "!@#$%^&*"),
            'password' => $password
        ];

        return $this->deliver();
    }

    public function sendReviewRequest($email, $body)
    {
        $this->mailer->send([], [], function ($message) use ($email, $body)  {
            $message
                ->from($this->fromAddress, $this->fromName)
                ->to($email)
                ->subject("Review Request from DryformsPlus")
                ->setBody($body, 'text/html');
        });
    }

    public function deliver()
    {
        $this->mailer->send($this->view, $this->data, function($message) {
            $message->from($this->fromAddress, $this->fromName)
                    ->to($this->to)->subject($this->subject);
        });
    }
}