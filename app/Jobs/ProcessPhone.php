<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessPhone implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected User $user;
    protected  $random4Digits;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $random4Digits)
    {
        $this->user = $user;
        $this->random4Digits = $random4Digits;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Http::post('https://rest.nexmo.com/sms/json', [
            "from"=>"Julio Cesar Tovar",
            'api_key' => "e630d1a8",
            'api_secret' => "cL5tFVfss1mWz9St",
            'to' => "+52{$this->user->numero_telefono}",
            'text' => "Tu codigo de verificacion es: {$this->random4Digits}, sigue las instrucciones en tu correo electronico",
        ]);
    }
}
