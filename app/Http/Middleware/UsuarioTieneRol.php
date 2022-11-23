<?php
 
namespace App\Http\Middleware;
 
use Closure;
 
class UsuarioTieneRol
{
   
    public function handle($request, Closure $next, ...$roles)
    {   
        foreach($roles as $role)
            if ($request->user()->tieneRol($role)) return $next($request);
            
        abort(403, 'No eres un usuario autorizado');
    }
 
}