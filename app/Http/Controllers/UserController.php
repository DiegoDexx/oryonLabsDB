<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
//hash import
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
   public function login(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credenciales incorrectas.'], 401);
        }
    
        $token = $user->createToken('auth_token_' . $user->id)->plainTextToken;
    
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone, // Teléfono
                'roles' => $user->getRoleNames(), // Obtener roles del usuario con Spatie

            ],
            'redirect_url' => '' // URL opcional para el cliente
        ], 200);

    }

      public function logout(Request $request){
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
        } else {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }
    }
}
