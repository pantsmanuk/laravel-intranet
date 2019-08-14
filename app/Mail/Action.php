<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Action extends Mailable
{
    use Queueable, SerializesModels;

    public $holiday_action;
    public $holiday_dates;
    public $holiday_type;
    public $user_name;

    /**
     * Create a new message instance.
     *
     * @param array $request
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->holiday_action = $request['holiday_action'];
        $this->holiday_dates = $request['holiday_dates'];
        $this->holiday_type = $request['holiday_type'];
        $this->user_name = $request['user_name'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('donotreply@ggpsystems.co.uk', 'Holiday Booking Page')
            ->subject($this->holiday_action.': '.$this->holiday_type.' holiday request for '.$this->user_name
                .' on '.$this->holiday_dates)
            ->view('emails.action');
    }
}
