<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ShopController;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->role = $request->role;
        $user->save();

        return back()->with('success', "Le rôle de {$user->name} a été mis à jour !");
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', "L’utilisateur {$user->name} a été supprimé !");
    }
}
