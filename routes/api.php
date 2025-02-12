<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| API Routes
|----------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route pour obtenir l'utilisateur authentifié (utilise Sanctum pour l'authentification)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes publiques pour l'authentification
Route::group(['controller' => AuthController::class], function () {
    Route::post('/register', 'register'); // Route d'inscription
    Route::post('/login','login'); // Route de connexion
});

// Route pour l'envoi d'un message (public, mais peut être protégé par le middleware auth:sanctum si besoin)
Route::post('/send-message', [MessageController::class, 'send']);


// Routes liées aux utilisateurs
Route::group(['controller' => UserController::class], function () {
    Route::post('/user', 'store');      // Créer un utilisateur
    Route::get('/users', 'index');       // Récupérer la liste des utilisateurs
    Route::get('/user/{id}', 'show');     // Récupérer un utilisateur par son ID
    Route::put('/user/{id}', 'update');   // Mettre à jour un utilisateur
    Route::delete('/user/{id}', 'destroy'); // Supprimer un utilisateur
});

// Routes liées aux fichiers
Route::group(['controller' => FileController::class], function () {
    Route::post('/file', 'store');      // Ajouter un fichier
    Route::post('/files', 'storeFiles');// Ajouter plusieurs fichiers
    Route::get('/files', 'index');       // Récupérer la liste des fichiers
    Route::get('/file/{id}', 'show');     // Récupérer un fichier par son ID
    Route::put('/file/{id}', 'update');   // Mettre à jour un fichier
    Route::delete('/file/{id}', 'destroy'); // Supprimer un fichier
});

// Routes liées aux messages
Route::group(['controller' => MessageController::class], function () {
    Route::post('/message', 'store');
    Route::get('/messages', 'index');
    Route::get('/message/{id}', 'show'); 
    Route::put('/message/{id}', 'update');
    Route::delete('/message/{id}', 'destroy');
    Route::get('/messages/user/{id}', 'getMessagesFromUserId');
    Route::post('/messages-between-date',  'getMessagesFromBetweenDatesOrUserId');
});

// Routes liées à l'historique
Route::group(['controller' => HistoryController::class], function () {
    Route::post('/history', 'store');      // Ajouter un historique
    Route::get('/histories', 'index');       // Récupérer la liste des historiques
    Route::get('history/{id}', 'show');     // Récupérer un historique par son ID
    Route::put('history/{id}', 'update');   // Mettre à jour un historique
    Route::delete('history/{id}', 'destroy'); // Supprimer un historique
});



