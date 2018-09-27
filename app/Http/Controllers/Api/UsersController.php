<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Users\UserStore;
use App\Http\Requests\Users\UserUpdate;
use App\Models\Team;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Hashing\Hasher;
use Invi;
use App\Mail\AppMailer;

class UsersController extends ApiController
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Team
     */
    private $team;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * UsersController constructor.
     *
     * @param User $user
     * @param Team $team
     * @param Hasher $hasher
     */
    public function __construct(User $user, Team $team, Hasher $hasher)
    {
        $this->user = $user;
        $this->team = $team;
        $this->hasher = $hasher;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->user->with(['role', 'teams'])
            ->where('company_id', auth()->user()->company_id)
            ->paginate(20);

        return $this->respond($users);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->user
            ->with(['role', 'teams'])
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        return $this->respond($user);
    }

    /**
     * @param UserStore $request
     *
     * @return JsonResponse
     */
    public function store(UserStore $request, AppMailer $mailer): JsonResponse
    {
        $userData = $request->validatedOnly();
        if ($request->has('team_id')) {
            unset($userData['team_id']);
        }
        $password = str_random(12);
        $userData['password'] = bcrypt($password);
        // $code = json_decode(Invi::generate($request->get("email"), "2 day", true), TRUE);
        // if (array_key_exists('error', $code)) {
        //     return $this->respondWithError([
        //         'message' => $code['error']
        //     ],
        //         422
        //     );
        // }
        $fullName = $request->get('first_name') . ' '. $request->get('last_name');
        // $mailer->sendInvitation($fullName, $request->get("email"), $password, $code, 2);
        $mailer->sendInvitation($fullName, $request->get("email"), $password, 2);
        $user = $this->user->create($userData);
        if ($request->has('team_id')) {
            $user->teams()->attach($request->get('team_id'));
        }

        return $this->respond(['message' => 'User successfully created', 'user' => $user]);
    }

    /**
     * @param UserUpdate $request
     *
     * @return JsonResponse
     */
    public function update(UserUpdate $request): JsonResponse
    {
        $userData = $request->validatedOnly();
        if ($request->has('team_id')) {
            unset($userData['team_id']);
        }
        $user = $this->user->find($request->input('id'));
        $user->update($userData);

        if ($request->has('team_id')) {
            $user->teams()->sync($request->get('team_id'));
        } else {
            $user->teams()->detach();
        }

        return $this->respond(['message' => 'User successfully updated', 'user' => $user]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->user->findOrFail($id);
        if ($user) {
            Invitation::where('email', $user['email'])->delete();
        }        
        $user->delete();

        return $this->respond(['message' => 'User successfully deleted']);
    }
}