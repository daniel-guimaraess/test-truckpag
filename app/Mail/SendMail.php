<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $alert;
    public $data;
    public $hora;

    public function __construct($subject, $alert, $data, $hora)
    {
        $this->subject = $subject;
        $this->alert = $alert;
        $this->data = $data;
        $this->hora = $hora;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.custom')
                    ->with([
                        'alert' => $this->alert,
                        'data' => $this->data,
                        'hora' => $this->hora,
                    ]);
    }
}
