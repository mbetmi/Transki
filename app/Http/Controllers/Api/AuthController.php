<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User; // Importer le modèle User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importer la facade Auth
use Illuminate\Support\Facades\Hash; // Importer Hash pour les mots de passe
use Illuminate\Support\Facades\Validator; // Importer le validateur
use Illuminate\Support\Str;


class AuthController extends Controller
{
    // Méthode pour enregistrer un nouvel utilisateur
    public function register(Request $request)
    {
        // Validation des données de la requête
        try 
        {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8',
                'department_name' => 'required|string|max:255',
                'type' => 'required', 
            ]);

            $user = User::create([
                'name' => ucwords(strtolower(trim($request['name']))),
                'email' => trim($request['email']),
                'password' => bcrypt(trim($request['password'])),
                'department_name' => trim($request['department_name']),
                'type' => trim($request['type']),  
            ]);

            $token = $user->createToken('main')->plainTextToken;

            $user = new UserResource($user);

            return response(compact('user','token'));
        
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['error' => 'An error occurred while processing your request'], 500);
            }
        }
        
    // Méthode pour la connexion de l'utilisateur
    public function login(Request $request)
    {
        // Validation des données de la requête
        $request->validate([
            'email' => 'required|string|email|regex:/@kokitechgroup\.cm$/i|exists:users,email',
            'password' => 'required|string',
        ]);

        // Tentative de connexion avec les identifiants
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email or password is incorrect.'
            ], 422);
        }

        // Récupérer l'utilisateur authentifié
        /** @var User $user */
        $user = Auth::user();

        // Vérification de l'état du compte utilisateur
        if ($user->state == 'waiting_for') {
            return response()->json([
                'message' => 'Account is waiting for validation.',
                'state' => $user->state
            ], 403); // Utilisation du code 403 pour une action interdite
        }

        if ($user->state == 'idle') {
            return response()->json([
                'message' => 'Account is currently idle.',
                'state' => $user->state
            ], 403); // Utilisation du code 403 pour une action interdite
        }

        // Génération du token pour l'utilisateur authentifié
        $token = $user->createToken('main')->plainTextToken;

        // Retourner les informations de l'utilisateur et le token d'accès
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
