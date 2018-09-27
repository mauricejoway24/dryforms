<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoResponders\AutoResponderUpdate;
use App\Mail\AutoResponderMail;
use App\Models\AutoResponder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Prologue\Alerts\Facades\Alert;

class AutoRespondersController extends Controller
{
    /**
     * @var AutoResponder
     */
    private $autoResponder;

    /**
     * AutoRespondersController constructor.
     * @param AutoResponder $autoResponder
     */
    public function __construct(AutoResponder $autoResponder)
    {
        $this->autoResponder = $autoResponder;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $autoResponders = $this->autoResponder->all();

        return view('dashboard.auto-responders.index', compact('autoResponders'));
    }

    public function preview(int $id)
    {
        $autoResponder = $this->autoResponder->findOrFail($id);

        return (new AutoResponderMail($autoResponder))->render();
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(int $id): View
    {
        $autoResponder = $this->autoResponder->findOrFail($id);

        return view('dashboard.auto-responders.form', compact('autoResponder'));
    }

    /**
     * @param int $id
     * @param AutoResponderUpdate $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(int $id, AutoResponderUpdate $request): RedirectResponse
    {
        $autoResponder = $this->autoResponder->findOrFail($id);
        $autoResponder->update($request->only(['title', 'description', 'content']));

        Alert::success('Auto Responder successfully updated')->flash();

        return redirect()->back()->with('alerts', Alert::all());
    }
}