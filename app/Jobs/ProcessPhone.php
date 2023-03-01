<?php

namespace App\Jobs;


use Exception;
use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessPhone implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $phone;
    private string $message;
    /**
     * Create a new job instance.
     */
    public function __construct(string $phone, string $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     *@throws Exception
     */

    public function handle(): void
    {
        $SID = env('TWILIO_SID');
        $TOKEN = env('TWILIO_TOKEN');
        if (empty($SID) || empty($TOKEN))
            throw new Exception('TWILIO_SID or TWILIO_TOKEN is not set');

        $TO = "+52".$this->phone;
        Log::info("Sending sms to $TO with message $this->message");
        $FROM = +13085299039;
        $response = Http::asForm()->withBasicAuth($SID, $TOKEN)
            ->post('https://api.twilio.com/2010-04-01/Accounts/'.$SID.'/Messages.json',[
                'To' => $TO,
                'From' => $FROM,
                'Body' => $this->message
                ]
               );
        if ($response->ok() || $response->status() == 201)
            Log::info("SMS sent to $TO successfully");

    }
}
