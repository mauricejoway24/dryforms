<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\Account\ChangeEmailRequest;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\SubscribeRequest;
use App\Http\Requests\Account\UpdateSourceRequest;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Auth\AuthManager;
use Stripe\Plan;
use Stripe\Stripe;

class AccountController extends ApiController
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var AuthManager
     */
    private $authManager;

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * AccountController constructor.
     * @param User $user
     * @param Hasher $hasher
     * @param AuthManager $authManager
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(User $user, Hasher $hasher, AuthManager $authManager, SubscriptionService $subscriptionService)
    {
        $this->user = $user;
        $this->hasher = $hasher;
        $this->authManager = $authManager;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = $this->authManager->user();
        $user->load(['role', 'company', 'teams']);

        return $this->respond([
            'user' => $user,
            'isSubscribed' => $user->isSubscribed(),
            'isGracePeriod' => $user->isOnTrial(),
            'company' => $user->company
        ]);
    }

    /**
     * @param ChangePasswordRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $this->authManager->user();
        if (!($this->hasher->check($request->get('old_password'), $user->password))) {
            return $this->respondWithError([
                'message' => 'Your current password does not matches with the password you provided.'
            ],
                422
            );
        }
        
        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return $this->respond(['message' => 'Password was successfully changed']);
    }

    /**
     * @param ChangeEmailRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeEmail(ChangeEmailRequest $request)
    {
        if ($this->authManager->user()->email !== $request->get('old_email')) {
            return $this->respondWithError(['message' => 'Old email mismatch'], 422);
        }
        $user = $this->user->where('email', $request->get('old_email'))->first();
        $user->email = $request->get('new_email');
        $user->save();

        return $this->respond(['message' => 'Email was successfully changed']);
    }

    /**
     * @param UpdateSourceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSource(UpdateSourceRequest $request)
    {
        $user = $this->authManager->user();

        try {
            $this->subscriptionService->changeSource($user, $request->get('stripeToken'));
        } catch (\Exception $e) {
            $this->respondWithError(['message' => $e->getMessage()], 400);
        }

        return $this->respond(['message' => 'Credit card details successfully updated']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelSubscription(Request $request)
    {
        $user = $this->authManager->user();

        try {
            $this->subscriptionService->cancelSubscription($user);
        } catch (\Exception $e) {
            return $this->respondWithError(['message' => $e->getMessage()], 400);
        }

        return $this->respond(['message' => 'Your Subscription has been canceled.']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resumeSubscription(Request $request)
    {
        $user = $this->authManager->user();

        try {
            $this->subscriptionService->resume($user);
        } catch ( \Exception $e) {
            return $this->respondWithError(['message' => $e->getMessage()], 422);
        }
        return $this->respond(['message' => 'Glad to see you back. Your Subscription has been resumed.']);
    }

/*------ testing code -------*///TODO WTF?
    /**
     * @param SubscribeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(SubscribeRequest $request)
    {
        $stripeToken = $request->get('stripeToken');

        $pickedPlan = "dryforms";
        $me = auth()->user();
        try {
            if( $me->subscribed('DryForms') && ! $me->subscribedToPlan($pickedPlan, 'DryForms') ) {
                $me->subscription('DryForms')->swap($pickedPlan);
            } else {
                if( $coupon = $request->get('coupon') ) {
                    $me->newSubscription( 'DryForms', $pickedPlan)
                        ->withCoupon($coupon)
                        ->create($request->get('stripeToken'), [
                            'email' => $me->email
                        ]);

                } else {
                    $me->newSubscription( 'DryForms', $pickedPlan)->create($request->get('stripeToken'), [
                        'email' => $me->email,
                        'description' => $me->first_name. ' '. $me->last_name
                    ]);
                }
            }
        } catch (\Exception $e) {
            return $this->respondWithError(['message' => $e->getMessage()], 422);
        }
        return $this->respond(['message' => 'You are now subscribed']);    
    }

    public function getInvoices(Request $request)
    {
        $me = auth()->user();
        $invoices = $me->invoicesIncludingPending();
        //$invoices = ($me->subscribed('DryForms') or $me->subscription('DryForms')->cancelled()) ? $me->invoices() : null;
        return $this->respond(['message' => $invoices]);
    }
/*---------------------------*/

}