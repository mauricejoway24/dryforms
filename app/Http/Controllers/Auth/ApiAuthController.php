<?php
namespace App\Http\Controllers\Auth;

use App\Events\UserRegistered;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Stripe\Error\InvalidRequest;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends LoginController
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    /**
     * @var Connection
     */
    private $db;

    private $subscriptionService;

    /**
     * ApiLoginController constructor.
     *
     * @param User $user
     * @param JWTAuth $jwtAuth
     */
    public function __construct(User $user, JWTAuth $jwtAuth, Connection $db, SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->db = $db;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $this->jwtAuth->attempt($credentials);

            if (!$token) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to create token'
            ], 401);
        }
        
        return response()->json([
            'token' => $token,
        ]);
    }

    /**
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $tokenData = $request->get('stripeToken');

        try {
            $this->db->beginTransaction();
            $user = $this->user->create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'zip' => $request->input('zip'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'role_id' => Role::ADMIN,
                'verified' => 1
            ]);

            $this->subscriptionService->subscribe($user, $tokenData, $request->get('coupon'));
            event(new UserRegistered($user));
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
        $token = $this->jwtAuth->fromUser($user);

        $this->guard()->login($user);

        return response()->json([
            'message' => 'Welcome to DryForms!',
            'token' => $token,
            'id' => $user->id
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = $this->jwtAuth->parseToken()->refresh();
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        }

        return response()->json([
            'token' => $token,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws JWTException
     */
    public function logout(Request $request)
    {
        $this->jwtAuth->parseToken()->invalidate();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser(){
        $user = $this->jwtAuth->toUser();

        return response()->json(['result' => $user]);
    }
}