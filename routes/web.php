<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocusignController;


Route::get('/', [DocusignController::class, 'index']);
Route::get('/docusign', [DocusignController::class, 'docusign']);
Route::get('/getUsers', [DocusignController::class, 'getUsers']);