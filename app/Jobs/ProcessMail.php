<?php

namespace App\Jobs;

use App\Mail\SendMail;
use App\Models\User;
use Error;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessMail implements ShouldQueue
{
    protected User $user;
    protected $url;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }
    
        //
    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::post("https://api.smtp2go.com/v3/email/send", [
            "api_key"=> "api-DEBF97B2AE1A11ED8C52F23C91C88F4E",
            "to"=> ["{$this->user->name} <{$this->user->email}>"],
            "sender" => "Dark Souls API <caesarnetyet@gmail.com>",
            "subject" => "Dark Souls API",
            "html_body" => view('email.welcome', ['user' => $this->user, 'url' => $this->url])->render()
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
        if ($response->successful()){
            Log::info('Mail Sent');
        } else {
            
            Log::error('Mail not sent');
            Log::alert($response->body());
            trigger_error($response->body(), E_USER_ERROR);
        }
        // Mail::to($this->user->email)->send(new SendMail($this->user, $this->url));
        // Log::info('Mail Sent');
    }
}
