<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie que l'utilisateur est connecté
        if (!Auth::check()) {
            // redirection vers login si non connecté
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();

        // Vérifie si la colonne existe bien et vaut true/1
        if (empty($user->role != 'admin')) {
            // Ici tu peux soit abort, soit rediriger
            abort(403, 'Accès refusé : vous n\'êtes pas administrateur.');
        }

        // Si tout est ok => on laisse passer
        return $next($request);
    }
}
