<?php
 
namespace App\Http\Middleware;
 
use Closure;
 
class UsuarioTieneRol
{
   
    public function handle($request, Closure $next, $role)
    {   
     
        if (! $request->user()->tieneRol($role)) 
            return abort(401,'No esta autorizado para realizar esta accion');
        
 
        return $next($request);
    }
 
}