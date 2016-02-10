<?php

namespace App\Mailers;

use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;

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
        $admin = User::find(1);
        $this->to      = $admin->email;
        $this->subject = $note;
        $this->view    = 'auth.emails.admin';
        $this->data    = compact('user','note');

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
                 $message->from(   $this->from, 'Administrator' )
                         ->to(     $this->to )
                         ->subject($this->subject );
            }
        );
    }


}
