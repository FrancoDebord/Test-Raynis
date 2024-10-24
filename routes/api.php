<?php

// use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthController;
// use App\Http\Controllers\TacheController;
use Illuminate\Http\Request;
// use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(["middleware"=>["auth:sanctum"]], function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('creer-tache', [TaskController::class, 'store']);
   
    // Recuperer toutes les taches

    Route::post('recuperer-tache', [TaskController::class, 'index']);
    Route::post('supprimer-tache/{id}', [TaskController::class, 'desploy']);

    //Afficher une tache specifique
    Route::get('tache-specifique/{id}', [TaskController::class, 'show']);
    Route::get('mettre-jour/{id}', [TaskController::class, 'update']);    



});
