<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        return FileResource::collection(File::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        try
        {
            $request->validate([
                'file' => 'required', 
                'type' => 'required|string',  
            ]);
         
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $currentDateTime = now()->format('Y-m-d_H-i-s');
                $milliseconds = round(microtime(true) * 1000);
                $filename = "FILE-{$currentDateTime}-{$milliseconds}.{$file->getClientOriginalExtension()}";
      
                $path = $file->storeAs('avatars', $filename, 'public');  
    
                $fileRecord = File::create([
                    'name' => $filename, 
                    'type' => trim($request['type']), 
                    'size' => $file->getSize(), 
                ]);
    
                return response()->json([
                    'message' => 'File uploaded successfully',
                    'file' => [
                        'id' => $fileRecord->id,
                        'name' => $fileRecord->name,
                        'type' => $fileRecord->type,
                        'size' => $fileRecord->size,
                        'path' => $path,
                    ]
                ]);
            } else {
                return response()->json(['message' => 'No file uploaded'], 400);
            }
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeFiles(Request $request)
    {
        try 
        {
            $request->validate([
                'files' => 'required',
                'type' => 'required|string',
            ]);

            $uploadedFiles = [];

            if ($request->hasFile('files')) {
                
                foreach ($request->file('files') as $file) {
                    $currentDateTime = now()->format('Y-m-d_H-i-s');
                    $milliseconds = round(microtime(true) * 1000);
                    $filename = "FILE-{$currentDateTime}-{$milliseconds}.{$file->getClientOriginalExtension()}";
                    $file->storeAs('avatars', $filename, 'public');
                    File::create([
                        'name' => $filename,
                        'size' => $file->getSize(),
                        'type' => trim($request['type']),
                    ]);
                  
                }
            }

            return response()->json(['message' => 'Files added successfully',  'file_ids' => $uploadedFiles ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function show($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'file not found'], 404);
        }

        return new FileResource($file);  
    }

    public function update(Request $request, $id)
    {
        try
        {
            $file = File::find($id);

            if (!$file) {
                return response()->json(['file' => 'file not found'], 404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'size' => 'required|string|max:255'
            ]);

            $file->update([
                'name' => $request['name'],     
                'type' => $request['type'],
                'size' => $request['size'],
            ]);

            return response()->json(['file' => 'file mis à jour avec succès']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }

    public function destroy($id)
    {
        $file = File::findOrFail($id);
    
        if (!$file) {
            return response()->json(['message' => 'file not found'], 404);
        }
    
        $hasMessages = $file->exists() ;
       
        if ($hasMessages) {
            return response()->json(['message' => 'Cannot delete file.'], 400);
        }
    
        if ($file->delete()) {
            return response()->json(['message' => 'file deleted successfully']);
        }
    
        return response()->json(['message' => 'Failed to delete user'], 500);
    }
}
