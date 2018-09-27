<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Mail\AppMailer;
use Illuminate\Support\Facades\Auth;

class TicketCommentsController extends Controller
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

    public function postComment(Request $request, AppMailer $mailer)
    {
        $this->validate($request, [
            'comment'   => 'required'
        ]);

        $ticketComment = $this->ticketComment->create([
            'ticket_id' => $request->input('ticket_id'),
            'user_id'   => Auth::user()->id,
            'comment'   => $request->input('comment')
        ]);

        // send mail if the user commenting is not the ticket owner
        if ($ticketComment->ticket->user->id !== Auth::user()->id) {
            $mailer->sendTicketComments($ticketComment->ticket->user, Auth::user(), $ticketComment->ticket, $ticketComment);
        }

        return redirect()->back()->with("status", "Your comment has be submitted.");
    }
}
