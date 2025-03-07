<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documentos/crear', [DocumentController::class, 'create'])->name('documents.create');
Route::post('/documentos', [DocumentController::class, 'store'])->name('documents.store');
