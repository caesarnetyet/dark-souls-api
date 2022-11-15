<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $url;


    public function __construct(User $user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }
   
    public function build()
    {
        return $this->view('email.welcome')
        ->with([
            'name' => $this->user->name,
            'email' => $this->user->email,
            'url' => $this->url,
        ]); 
    }
}
