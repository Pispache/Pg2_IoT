<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user=$request->user();
        abort_if(!$user || !$user->is_active, 403, 'Usuario inactivo o no autenticado.');
        abort_unless($user->hasRole(...$roles), 403, 'No tiene permisos para realizar esta acción.');
        return $next($request);
    }
}
