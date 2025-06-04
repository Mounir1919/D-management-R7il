<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        // return response()->json(true);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', //|unique:users pour eviter les doublons
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::Create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'success' => "Utilisateur s'inscrit avec succes",
            'User' => $user
        ], 201); //201 indique que la requête a été réussie et que la ressource a été créée
    }

    public function login(Request $request)
    {
        // Valider les données reçues du client (email et mot de passe)
        $request->validate([
            'email' => 'required|string|email', //|unique:users pour eviter les doublons
            'password' => 'required|string',
        ]);
        //  Auth::attempt() retourne true si l'email existe et que le mot de passe correspond (avec bcrypt)
        // si l'authentification echoue (email et pswd ne sont pas les memes dans la bd)
        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json([
                'message' => 'Email et mot de passe sont incorrects',
            ], 401); //401 unauthorized
        // Récupérer l'utilisateur connecté à partir de son email
        $user = User::where('email', $request->email)->FirstOrFail();
        // Créer un token API avec Laravel Sanctum pour authentifier les prochaines requêtes
        // $token =  $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => "Utilisateur est connecte avec succes",
            'User' => $user,
            // 'Token' => $token
        ], 201);
    }
    public function logout(Request $request)
    {
        // Supprime le token actuel utilisé par l'utilisateur
        // Cela signifie que l'utilisateur est "déconnecté" (il ne pourra plus accéder aux routes protégées)
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => "Logout effectue avec succes",
        ], 201);
    }
};
