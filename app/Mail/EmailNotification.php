<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class EmailNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * various data instances
     */
    public $data;
    public $viewname;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $viewname, $data )
    {
        // assign values
        $this->data     = $data;
        $this->viewname = $viewname;
        $this->subject  = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view(   $this->viewname)
            ->subject($this->subject)
            ->with(   $this->data);
    }
}
