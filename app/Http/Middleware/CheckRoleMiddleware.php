<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\Auth;


// class CheckBoutiquierRole
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  \Closure  $next
//      * @param  string  $role
//      * @return mixed
//      */

//     public function handle(Request $request, Closure $next, $role)
//     {
//         /** @var \App\Models\User $user **/

//         $user = Auth::user();

//         if (!$user || !$user->hasRole($role)) {
            
//             return response()->json([
//                 'status' => 'ECHEC',
//                 'message' => 'Accès refusé. Vous devez être un ' . $role . ' pour accéder à cette ressource.'
//             ], 403);
//         }

//         return $next($request);
//     }
// }

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Facades\RoleServiceFacade as RoleFacade;    


class CheckRoleMiddleware
{

    public function handle(Request $request, Closure $next, $roles)
    {


        $roles = explode(',', $roles);
        // dd($roles, Auth::user(),  RoleFacade::getRoleById(Auth::user()->roleId));    
        $roleLibelle = RoleFacade::getRoleById(Auth::user()->roleId);

        if (Auth::check() && in_array($roleLibelle, $roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}