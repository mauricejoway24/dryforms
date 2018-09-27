<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoRespondersTable extends Migration
{
    const SLUG = 'slug';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const CONTENT = 'content';

    const DUMMY_CONTENT = 'Enter message here';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_responders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('title');
            $table->string('description')->nullable();
            $table->text('content', 10000);
            $table->boolean('active')->default(true);
        });

        \DB::table('auto_responders')->insert([
            [
                static::SLUG => '14_days_free_trial',
                static::TITLE => '14 Day Free Trial',
                static::DESCRIPTION => 'Welcome message for users that get free trial, with coupon.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => '7_day_reminder_trial',
                static::TITLE => '7 Day Reminder on 14-Day Free Trial',
                static::DESCRIPTION => '7 days reminder, before payment is taken.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => '3_day_reminder_trial',
                static::TITLE => '3 Day Reminder on 14-Day Free Trial ',
                static::DESCRIPTION => '3 days reminder about payment will be taken.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => 'welcome_for_paid_user',
                static::TITLE => 'Welcome – PAID membership message ',
                static::DESCRIPTION =>'',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => '14_days_after_subscribing',
                static::TITLE => '14 Days After Subscribing',
                static::DESCRIPTION => '14 days after 1st PAID subscribing. Only send once, never again.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => 'support_ticket',
                static::TITLE => 'Support Ticket ',
                static::DESCRIPTION => 'Support Ticket – for when ticket is submitted.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => 'suggestion_ticket',
                static::TITLE => 'Suggestion Ticket ',
                static::DESCRIPTION => 'Suggestion Ticket – for when suggestion is submitted.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => 'new_mailing_list_subscriber',
                static::TITLE => 'New Mailing List Subscriber ',
                static::DESCRIPTION => 'This message is sent when someone subscribes for mailing list.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => 'unsubscribe',
                static::TITLE => 'Unsubscribe from Mailing List',
                static::DESCRIPTION => 'Unsubscribe from Mailing List.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => 'account_suspended',
                static::TITLE => ' Account Suspended',
                static::DESCRIPTION => 'When payment is declined or whenever account has been cancelled or suspended.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
            [
                static::SLUG => 'credit_card_expiration',
                static::TITLE => 'Credit Card Expiration Reminder ',
                static::DESCRIPTION => 'Credit Card Expiration Reminder - to be sent 15 days before credit card expiration, to remind user to update CC.',
                static::CONTENT => static::DUMMY_CONTENT,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_responders');
    }
}
