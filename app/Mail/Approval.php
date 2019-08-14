<?php

namespace App\Mail;

use App\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Approval extends Mailable
{
    use Queueable, SerializesModels;

    public $holiday_dates;
    public $holiday_id;
    public $holiday_note;
    public $holiday_overlaps;
    public $holiday_type;
    public $user_name;
    public $uuid;

    /**
     * Create a new message instance.
     *
     * @param array $request
     * @return void
     */
    public function __construct($request)
    {
        $this->holiday_dates = $request['holiday_dates'];
        $this->holiday_id = $request['holiday_id'];
        $this->holiday_note = $request['holiday_note'];
        $this->holiday_overlaps = $request['holiday_overlaps'];
        $this->holiday_type = $request['holiday_type'];
        $this->user_name = $request['user_name'];
        $this->uuid = (string)Str::orderedUuid();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        DB::insert("INSERT INTO uuid (id_bin, id) VALUES (UNHEX(REPLACE(?,'-','')), ?)", [$this->uuid, $this->holiday_id]);
        return $this->from(Config::where('name', '=', 'email_sender')->pluck('value')->first(), 'Holiday Booking Page')
            ->subject($this->holiday_type . ' holiday request for ' . $this->user_name . ' on ' . $this->holiday_dates)
            ->view('emails.approval');
    }
}
