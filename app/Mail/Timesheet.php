<?php

namespace App\Mail;

use App\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Timesheet extends Mailable
{
    use Queueable, SerializesModels;

    public $attachment_filename;
    public $timesheet_date;
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
        $this->attachment_filename = $request['attachment_filename'];
        $this->timesheet_date = $request['timesheet_date'];
        $this->user_name = $request['user_name'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(Config::where('name', '=', 'email_sender')
            ->pluck('value')
            ->first(), 'Intranet Attendance Page')
            ->attach(public_path($this->attachment_filename), [
                'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
            ->subject($this->timesheet_date.' Timesheet for '.$this->user_name)
            ->view('emails.timesheet');
    }
}
