<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoryResource;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // $histories = History::all();
        // return response()->json($histories);
        // return $histories->toJson(JSON_PRETTY_PRINT);
        return HistoryResource::collection(History::with(['message_id', 'file_id'])->orderBy('id', 'desc')->get());        
    }
    public function store(Request $request)
    {
        try { 
            $validated = $request->validate([
                'message_id' => 'required|exists:messages,id',
                'file_id' => 'required|exists:files,id', 
            ]);
        
            $history = History::create([
                'message_id' => trim($request['message_id']),
                'file_id' =>trim($request['file_id']),
            ]);
        
            return response()->json(['message' => ' historie successfully created!','data' => new HistoryResource($history),]);  
        
        } catch (\Illuminate\Validation\ValidationException $e) {
                
            return response()->json([
                'message' => 'Validation failed!',
                 'errors' => $e->errors()
            ], 422);  
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again later.',
                'error' => $e->getMessage()
            ], 500);  
        }
    }

    public function show($id)
    {
        $history = History::find($id);

        if (!$history) {
            return response()->json(['message' => 'file not found'], 404);
        }

        return new HistoryResource($history); 
    }

    public function update(Request $request, $id)
    {
        try {
            $history = History::find($id);

            if (!$history) {
                return response()->json(['history' => 'history not found'], 404);
            }

            $request->validate([
                'message_id' => 'required|integer',
                'file_id' => 'required|string|max:255'
            ]);
            $history->update([
                'message_id' => $request['message_id'],
                'file_id' => $request['file_id'],
            ]);
            
            return response()->json(['history' => 'history mis à jour avec succès']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }
    public function destroy($id)
    {
        // $message = History::findorfail($id);
        // History::destroy($id);

        $history=History::findOrFail($id);
        
        if($history->delete()){
            return response()->json(['message' => 'history supprimer avec succès']);
        }
    }
}
