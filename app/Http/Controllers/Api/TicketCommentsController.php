<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Mail\AppMailer;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TicketCommentsController extends ApiController
{
    /**
     * @var Ticket
     */
    private $ticket;

    /**
     * @var TicketComment
     */
    private $ticketComment;

    /**
     * Ticket constructor.
     *
     * @param Ticket $ticket
     * @param TicketComment $ticketComment
     */
    public function __construct(Ticket $ticket, TicketComment $ticketComment)
    {
        $this->ticket = $ticket;
        $this->ticketComment = $ticketComment;
    }

    /**
     * @param Request $request
     * @param AppMailer $mailer
     * 
     * @return JsonResponse
     */
    public function store(Request $request, AppMailer $mailer): JsonResponse
    {
        $this->validate($request, [
            'comment'   => 'required'
        ]);


        $ticketComment = $this->ticketComment->create([
            'ticket_id' => $request->input('ticket_id'),
            'user_id'   => auth()->user()->id,
            'comment'   => $request->input('comment')
        ]);

        if ($ticketComment->ticket->user->id !== auth()->user()->id) {
            $mailer->sendTicketComments($ticketComment->ticket->user, auth()->user(), $ticketComment->ticket, $ticketComment);
        }

        return $this->respond([
            'message' => "Your comment has be submitted.",
            'comment' => $ticketComment
        ]);
    }
}
