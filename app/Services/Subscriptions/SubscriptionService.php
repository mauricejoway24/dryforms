<?php

namespace App\Services\Subscriptions;

use App\Models\User;
use Stripe\Customer;
use Stripe\Source;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionService
{
    /**
     * @var Stripe
     */
    private $stripe;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * SubscriptionService constructor.
     * @param Stripe $stripe
     * @param Customer $customer
     * @param Subscription $subscription
     */
    public function __construct(Stripe $stripe, Customer $customer, Subscription $subscription)
    {
        $this->stripe = $stripe;
        $this->customer = $customer;
        $this->subscription = $subscription;

        $this->stripe->setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * @param User $user
     * @param array $tokenData
     * @param null|string $coupon
     * @return Subscription
     */
    public function subscribe(User $user, array $tokenData, ?string $coupon)
    {
        $customer = $this->createCustomer($user);
        $subscription = $this->createSubscription($customer, $tokenData, $coupon, true);
        $this->setStripeData($user, $customer, $tokenData, $subscription);

        return $subscription;
    }

    public function resume(User $user)
    {
        $customer = $this->customer->retrieve($user->stripe_id);
        $subscription = $this->createSubscription($customer, [], null);
        $this->setStripeData($user, $customer, [], $subscription);

        return $subscription;
    }

    /**
     * @param User $user
     */
    public function cancelSubscription(User $user)
    {
        $user->subscription->cancel();
    }

    /**
     * @param User $user
     * @param array $tokenData
     */
    public function changeSource(User $user, array $tokenData)
    {
        $customer = $this->customer->retrieve($user->stripe_id);
        $source = $customer->sources->create(['source' => $tokenData['id']]);
        $customer->default_source = $source->id;
        $customer->save();
        $this->setStripeData($user, $customer, $tokenData);
    }

    /**
     * @param User $user
     * @param Customer $customer
     * @param array $tokenData
     * @param null|Subscription $subscription
     */
    private function setStripeData(User $user, Customer $customer, array $tokenData, ?Subscription $subscription = null)
    {
        $card = $this->extractDefaultCard($customer);

        $user->stripe_id = $customer->id;
        $user->card_expiry = $card ? "{$card->exp_month}/{$card->exp_year}" : "{$tokenData['card']['exp_month']}/{$tokenData['card']['exp_year']}";
        $user->card_brand = $card ? $card->brand : $tokenData['card']['brand'];
        $user->card_last_four = $card ? $card->last4 : $tokenData['card']['last4'];
        $user->trial_ends_at = $subscription && $subscription->trial_end ? $subscription->trial_end : null;
        $user->save();
    }

    /**
     * @param Customer $customer
     * @param array $tokenData
     * @param null|string $coupon
     * @param bool|null $withTrial
     * @return Subscription
     */
    private function createSubscription(Customer $customer, array $tokenData, ?string $coupon, ?bool $withTrial = false)
    {
        $subscription = $this->subscription->create([
            'customer' => $customer->id,
            'coupon' => $coupon,
            'source' => $tokenData['id'] ?? $customer->default_source,
            'trial_from_plan' => $withTrial,
            'items' => [
                [
                    'plan' => env('DEFAULT_PLAN')
                ]
            ]
        ]);

        return $subscription;
    }

    /**
     * @param User $user
     * @return Customer
     */
    private function createCustomer(User $user)
    {
        $customer = $this->customer->create([
            'email' => $user->email
        ]);

        return $customer;
    }

    /**
     * @param Customer $customer
     * @return Source|null
     */
    private function extractDefaultCard(Customer $customer)
    {
        foreach ($customer->sources->data as $source) {
            if ($source->id === $customer->default_source) {
                return $source;
            }
        }

        return null;
    }
}