<?php

namespace App\Jobs;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;
    private string $url;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        $apiKey = env('SMTP2GO_API_KEY');
        if (empty($apiKey))
            throw new Exception('SMTP2GO_API_KEY is not set');

        $response = Http::post('https://api.smtp2go.com/v3/email/send', [
            'api_key' => $apiKey,
            'to' => ["{$this->user->name} <{$this->user->email}>"],
            "subject"=> "Email Verification",
            'sender' => "Dark Souls APP <caesarnetyet@gmail.com>",
            "html_body" => view('email.register', ['link' => $this->url])->render()
        ]);
        Log::info("Email sent to {$this->user->email} successfully {$response->status()}");
    }
}
