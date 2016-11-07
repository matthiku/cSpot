<?php

namespace App\Mailers;

use App\Models\User;
use App\Models\Plan;
use App\Models\Team;

use Illuminate\Contracts\Mail\Mailer;
use Auth;

class AppMailer
{

    /**
     * The Laravel Mailer instance.
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * The sender of the email.
     *
     * @var string
     */
    protected $from = 'admin@example.com';

    /**
     * The recipient of the email.
     *
     * @var string
     */
    protected $to;

    /**
     * The recipient of the email.
     *
     * @var string
     */
    protected $cc;

    /**
     * The subject of the email.
     *
     * @var string
     */
    protected $subject;

    /**
     * The view for the email.
     *
     * @var string
     */
    protected $view;

    /**
     * The data associated with the view for the email.
     *
     * @var array
     */
    protected $data = [];




    /**
     * Create a new app mailer instance.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }



    /**
     * Deliver the email confirmation.
     *
     * @param  User $user
     * @return void
     */
    public function sendEmailConfirmationTo(User $user)
    {
        $this->to      = $user->email;
        $this->cc      = 'church.ennis@gmail.com';
        $this->subject = "Email address confirmation for c-SPOT app";
        $this->view    = 'auth.emails.confirm';
        $this->data    = compact('user');

        $this->deliver();
    }



    /**
     * Notify Admin via email 
     *
     * @param  User $user
     * @return void
     */
    public function notifyAdmin(User $user, $note)
    {
        // not needed on local dev installations...
        if (env('APP_ENV')=='local') return;

        $this->to      = findAdmins('email');
        $this->cc      = 'church.ennis@gmail.com';
        $this->subject = $note;
        $this->view    = 'auth.emails.admin';
        $this->data    = compact( 'user', 'note' );

        $this->deliver();
    }


    /**
     * Notify Leader or teacher of a plan
     *
     * @param  
     * @return void
     */
    public function planReminder(User $recipient, Plan $plan, $role)
    {
        $user      = Auth::user();
        $this->cc  = findAdmins('email');
        $this->to  = $recipient->email;
        $this->subject = env('CHURCH_NAME', 'c-SPOT-App').' - missing items for your Service Plan';
        $this->view    = 'cspot.emails.reminder';
        $this->data    = compact( 'user', 'recipient', 'plan', 'role' );

        $this->deliver();
    }




    /**
     * Get confirmation for plan membership
     *
     * @param  
     * @return void
     */
    public function getPlanMemberConfirmation(User $recipient, Plan $plan, Team $team)
    {
        $user      = Auth::user();
        $this->cc  = findAdmins('email');
        $this->to  = $recipient->email;
        $this->subject = env('CHURCH_NAME', 'c-SPOT-App').' - please confirm your role on this Service plan';
        $this->view    = 'cspot.emails.staffConfirm';
        $this->data    = compact( 'user', 'recipient', 'plan', 'team' );

        $this->deliver();
    }


    /**
     * Deliver the email.
     *
     * @return void
     */
    public function deliver()
    {
        $this->mailer->send( 
            $this->view, 
            $this->data, 
            function ($message) {
                 $message->from(   $this->from, 'c-SPOT Administrator' )
                         ->to(     $this->to )
                         ->cc(     $this->cc )
                         ->subject($this->subject );
            }
        );
    }


}
