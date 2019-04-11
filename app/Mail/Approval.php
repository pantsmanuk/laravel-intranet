<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Approval extends Mailable
{
    use Queueable, SerializesModels;

    public $holiday_dates;
	public $holiday_id;
	public $holiday_nonce;
    public $holiday_note;
	public $holiday_overlaps;
    public $holiday_type;
    public $user_name;

    /**
     * Create a new message instance.
     *
	 * @param array		$request
     * @return void
     */
    public function __construct($request)
    {
    	$this->holiday_dates = $request['holiday_dates'];
		$this->holiday_id = $request['holiday_id'];
		$this->holiday_nonce = $request['holiday_note'];
		$this->holiday_note = $request['holiday_note'];
		$this->holiday_overlaps = $request['holiday_overlaps'];
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
        return $this->from('donotreply@ggpsystems.co.uk','Holiday Booking Page')
			->subject($this->holiday_type.' holiday request for '.$this->user_name.' on '.$this->holiday_dates)
			->view('emails.approval');
    }
}
