<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;

use Log;
use Mail;

class SendIntMsgNotification implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        //
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('trying to use the Queue to send internal messages notification');
        //
        $message = $this->message;

        $subject = 'c-SPOT internal message notification';
        $thread = Thread::find($message->thread_id);
        $thread_subject = $thread->subject;
        $message_body = $message->body;

        foreach ($thread->participants as $key => $recipient) {
            $user = $recipient->user;
            # check if user actually wants to be notified
            if ($user->notify_by_email) {

                Mail::send('cspot.emails.notification',
                    ['user'=>$user, 'subject'=>$subject, 'messi'=>$message],
                    function ($msg) use ($user, $subject) {
                        $msg->from(findAdmins()[0]->email, 'c-SPOT Admin');
                        $msg->to($user->email, $user->fullName);
                        $msg->subject($subject);
                    }
                );
            }
        }
    }
}
