<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // $users= User::all();
        // return response()->json($users);
        // return $users->toJson(JSON_PRETTY_PRINT);    
        return UserResource::collection(User::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'department_name' => 'required|string|max:255',
                'type' => 'required|string|max:255'
            ]);
    
            $user = User::create([
                'name' =>  trim($request['name']),
                'email' => trim($request['email']),
                'password' => bcrypt(trim($request['password'])),
                'department_name' => trim($request['department_name']),
                'type' => trim($request['type']),
            ]);

            return response()->json(['message' => 'user successfully created!','data' => new UserResource($user),], 201);  

        } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
                return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }
    
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'user not found'], 404);
        }
        return new UserResource($user);  
    }

    public function update(Request $request, $id)
    {  
         
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'user not found'], 404);
            }
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                // 'password' => 'required|string|max:255',
                // 'department_name' => 'required|string|max:255',
                // 'type' => 'required|string|max:255'
            ]);

            $user->update([
                'name' => $request['name'],
                'email' => $request['email'],
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => bcrypt(trim($request['password'])),
                ]);
            }
            return response()->json(['message' => 'user updated success']);
            
            // if($user->update($request->all())){
            //     return response()->json(['message' => 'user mis à jour avec succès']);
            // }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }

    // public function destroy($id){
        
    //     $user = User::find($id);
        
    //     if($user->delete()){
    //         return response()->json(['message' => 'User supprimer avec succès']);
    //     }
    // }
    public function destroy($id)
    {
        // Trouver l'utilisateur par son ID
        $user = User::find($id);
        // dd($user); // Vérifie si l'utilisateur est trouvé
    
        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Vérifier si l'utilisateur a envoyé des messages
        $hasMessages = $user->sentMessages()->exists() || $user->receivedMessages()->exists();
        // dd($hasMessages); // Vérifie si la condition renvoie le bon résultat
       
        if ($hasMessages) {
            // Si l'utilisateur a des messages, empêcher la suppression et renvoyer une erreur
            return response()->json(['message' => 'Cannot delete user because they have sent messages.'], 400);
        }
    
        // Si l'utilisateur n'a pas de messages, on peut procéder à la suppression
        if ($user->delete()) {
            return response()->json(['message' => 'User deleted successfully']);
        }
    
        return response()->json(['message' => 'Failed to delete user'], 500);
    }
    
    
}

// public function signup(Request $request)
    // {
    //    try {
    //         $request->validate([
    //             'lastname' => 'required|string',
    //             'firstname' => 'required|string',
    //             'email' => 'required|string|email|unique:users,email',
    //             'state' =>'required|string',
    //             'password' => 'required|string',
    //             'type' => 'required',
    //             'sexe' => 'required',
    //         ]);

    //         $user = User::create([
    //             'lastname' => ucwords(strtolower(trim($request['lastname']))),
    //             'firstname' => ucwords(strtolower(trim($request['firstname']))),
    //             'email' => trim($request['email']),
    //             'password' => bcrypt(trim($request['password'])),
    //             'type' => trim($request['type']),
    //             'state' => trim($request['state']),
    //             'sexe' => trim($request['sexe']),
    //             'contact_name' => trim($request['contact_name']),
    //             'contact_cni' => trim($request['contact_cni']),
    //             'contact_phone' => trim($request['contact_phone']),
    //             'contact_link' => trim($request['contact_link']),
    //         ]);

    //         $token = $user->createToken('main')->plainTextToken;

    //         // $token = Str::random(42);

    //         $user = new UserResource($user);

    //         return response(compact('user','token'));

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'An error occurred while processing your request'], 500);
    //     }
    // }