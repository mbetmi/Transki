<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        // $messages = Message::all();
        // return response()->json($messages);
        // return $messages->toJson(JSON_PRETTY_PRINT);
        // public function index()
        // {
        //     $messages = Message::with(['sender', 'receiver'])->get();
        //     return view('messages.index', compact('messages'));
        // }
        //  return Message::with(['sender', 'receiver'])->orderBy('id', 'desc')->get();        
    
        // return MessageResource::collection(Message::with(['sender', 'receiver','histories.file'])->orderBy('id', 'desc')->get());        

        // return MessageResource::collection(Message::with(['sender', 'receiver', 'files'])->orderBy('id', 'desc')->get()); 
       
    }
    
    public function store(Request $request)
    {
        try 
        {
            $request->validate([
                'sender_id' => 'required|exists:users,id',
                'receiver_id' => 'required|exists:users,id',
                'object' => 'required|string|max:255',
            ]);

            $message = Message::create([
                'sender_id' => trim($request['sender_id']),
                'receiver_id' =>trim($request['receiver_id']),
                'object' => trim($request['object']),
            ]);

            return response()->json(['message' => 'message successfully created!','data' => new MessageResource($message),], 201);  

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }

    public function show($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'message not found'], 404);
        }

        $message->load('sender', 'receiver', 'files');

        return new MessageResource($message);  
    }

    public function update(Request $request, $id)
    {
        try {
            $message = Message::find($id);

            if (!$message) {
                return response()->json(['message' => 'message not found'], 404);
            }
            
            $request->validate([
                'sender_id' => 'required|integer',
                'receiver_id' => 'required|integer',
                'object' => 'required|string|max:255'
            ]);
            $message->update([
                'sender_id' => $request['sender_id'],
                'receiver_id' => $request['receiver_id'],
                'object' => $request['object'],
            ]);

            return response()->json(['message' => 'message mis à jour avec succès']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }

    public function destroy($id)
    {
        
        $message = Message::with(['sender', 'receiver', 'files'])->find($id);
        
        if (!$message) {
            return response()->json(['message' => 'message not found'], 404);
        }
        
        $hasMessages = $message->exists();
        
        if ($hasMessages) {
            return response()->json(['message' => 'Cannot delete message .'], 400);
        }
        
        if ($message->delete()) {
            return response()->json(['message' => 'Message deleted successfully']);
        }
        
        return response()->json(['message' => 'Failed to delete user'], 500);
        
    }

    // public function send(Request $request)
    // {
    //     try {
    //         // Validation des données du formulaire
    //         $request->validate([
    //             'sender_id' => 'required|integer',
    //             'receiver_id' => 'required|exists:users,id',  // L'ID du destinataire doit être valide
    //             'object' => 'required|string',  // L'objet du message
    //             // 'content' => 'required|string',  // Le contenu du message
    //         ]);
           
    //         // Créer un nouveau message
    //         Message::create([
    //             'sender_id' => $request->sender_id,  // L'employé qui envoie le message
    //             'receiver_id' => $request->receiver_id,  // L'employé qui reçoit le message (ID passé dans le formulaire)
    //             'object' => $request->object,  // L'objet du message
    //         ]);
    //         // Rediriger ou renvoyer une réponse
    //         return response()->json(['message' => 'message enregistre avec succes']);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'An error occurred while processing your request'], 500);
    //     }
    // }

    public function getMessagesFromUserId($id)
    {
        $user = User::find($id);

        if (!$user) {
           return response()->json(['error' => 'User not found'], 404);
        }

        return MessageResource::collection(Message::with(['sender','receiver', 'files'])->where('sender_id', $id)->orWhere('receiver_id', $id)->orderBy('created_at', 'asc') ->get());
    }

    public function getMessagesFromBetweenDatesOrUserId(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date'
            ]);

            $startDate = trim($request->start_date);

            $endDate = trim($request->end_date);

            if (strlen($startDate) == 10) {
                $startDate .= ' 00:00:00';
            }

            if (strlen($endDate) == 10) {
                $endDate .= ' 23:59:59';
            }

            $query = Message::with(['sender','receiver', 'files']);

            if ($request->filled('user_id')) {
                $query->where('sender_id',trim($request->user_id))->orWhere('receiver_id',trim($request->user_id));
            }

            return MessageResource::collection($query->whereBetween('created_at',[$startDate,$endDate])->orderBy('created_at', 'desc')->get());

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }
    
    
}

