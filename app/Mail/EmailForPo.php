<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForPo extends Mailable
{
    use Queueable, SerializesModels;
    public $infos;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($infos)
    {
        $this->infos = $infos;
        $this->subject = $infos['subject'];
        $this->ref = $infos['reference_number'];
        $this->attachment = $infos['attachment'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject.$this->ref)
        ->view('emails.send-email-for-po')
        ->attach(public_path(). "/vendor/crudbooster/item_source/".$this->attachment);
    }
}
