<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Prologue\Alerts\Facades\Alert;

class TicketCategoriesController extends Controller
{
    /**
     * @var TicketCategory
     */
    private $ticketCategory;

    /**
     * Ticket constructor.
     *
     * @param TicketCategory $ticketCategory
     */
    public function __construct(TicketCategory $ticketCategory)
    {
        $this->ticketCategory = $ticketCategory;
    }

    public function index(Request $request)
    {
        $ticketCategories = $this->ticketCategory->all();
        return view('dashboard.ticket-categories.index', compact('ticketCategories'));
    }

    public function create()
    {
        return view('dashboard.ticket-categories.create', [
            'type' => 'Create'
        ]);
    }

    public function edit($id)
    {
        $ticketCategory = $this->ticketCategory->findOrFail($id);
        return view('dashboard.ticket-categories.create', [
            'type' => 'Edit',
            'category' => $ticketCategory
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->ticketCategory->find($id)->update($request->except(['_token']));

        Alert::success('Ticket Category successfully updated')->flash();

        return redirect()->route('ticket_categories.index')->with('alerts', Alert::all());
    }

    public function store(Request $request)
    {
        $this->ticketCategory->create($request->except(['_token']));

        Alert::success('Ticket Category successfully created')->flash();

        return redirect()->route('ticket_categories.index')->with('alerts', Alert::all());
    }

    public function destroy($id)
    {
        $ticketCategory = $this->ticketCategory->findOrFail($id);
        $ticketCategory->delete();

        Alert::success('TicketCategory successfully deleted')->flash();

        return redirect()->route('ticket_categories.index')->with('alerts', Alert::all());
    }
}
