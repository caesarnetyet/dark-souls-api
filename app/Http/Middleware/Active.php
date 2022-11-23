<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class Active
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        if ($request->user()) 
            if($request->user()->active) return $next($request);
            else abort(400, "Tu cuenta no ha sido activada");

        // dd($request->email);
        
        $user = User::where('email', $request->email)->first();
        if (!$user->active) abort(400, 'Tu cuenta no ha sido activada');
        return $next($request);
        
    }
}
