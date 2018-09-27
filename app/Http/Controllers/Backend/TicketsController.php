<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Mail\AppMailer;

class TicketsController extends Controller
{
    /**
     * @var Ticket
     */
    private $ticket;

    /**
     * @var TicketCategory
     */
    private $ticketCategory;

    /**
     * Ticket constructor.
     *
     * @param Ticket $ticket
     * @param TicketCategory $ticketCategory
     */
    public function __construct(Ticket $ticket, TicketCategory $ticketCategory)
    {
        $this->ticket = $ticket;
        $this->ticketCategory = $ticketCategory;
    }

    public function index(Request $request)
    {
        $ticketCategories = $this->ticketCategory->get(); 
        $tickets = $this->ticket
            ->with(['ticketCategory', 'user', 'comments'])
            ->paginate(10); 

        return view('dashboard.tickets.index', compact('tickets', 'ticketCategories'));
    }

    public function show(string $ticket_id)
    {
        $ticket = $this->ticket
            ->where('ticket_id', $ticket_id)->firstOrFail();

        $comments = $ticket->comments;
        $category = $ticket->ticketCategory;

        return view('dashboard.tickets.show', compact('ticket', 'category', 'comments'));
    }

    public function close($ticket_id, AppMailer $mailer)
    {
        $ticket = $this->ticket
            ->where('ticket_id', $ticket_id)->firstOrFail();

        $ticket->status = 'Closed';
        $ticket->save();
        $ticketOwner = $ticket->user;

        $mailer->sendTicketStatusNotification($ticketOwner, $ticket);
        return redirect()->back()->with("status", "The ticket has been closed.");
    }
}
