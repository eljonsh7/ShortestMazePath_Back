<?php

use App\Http\Controllers\GenerateMazeController;
use App\Http\Controllers\SolutionMazeController;
use App\Http\Controllers\MultiplayerMazeController;
use Illuminate\Support\Facades\Route;

Route::get('/generateMaze/{difficulty}',  [GenerateMazeController::class, 'generateMaze']);
Route::post('/solution/{solution}',  [SolutionMazeController::class, 'solution']);

Route::post('/multiplayer/storeMaze/{difficulty}',  [GenerateMazeController::class, 'storeMaze']);
Route::post('/multiplayer/getMaze/{maze}',  [MultiplayerMazeController::class, 'getMultiplayerMaze']);
Route::post('/multiplayer/beReady/{maze}',  [MultiplayerMazeController::class, 'beReady']);
Route::post('/multiplayer/finishMaze/{maze}',  [MultiplayerMazeController::class, 'finishMaze']);
