<?php

use App\Http\Controllers\GenerateMazeController;
use App\Http\Controllers\SolutionMazeController;
use Illuminate\Support\Facades\Route;

Route::get('/generateMaze/{difficulty}',  [GenerateMazeController::class, 'generateMaze']);

Route::post('/solution/{solution}',  [SolutionMazeController::class, 'solution']);
