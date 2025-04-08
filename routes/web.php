<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documentos', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documentos/crear', [DocumentController::class, 'create'])->name('documents.create');
Route::post('/documentos', [DocumentController::class, 'store'])->name('documents.store');
Route::get('/documentos/descargar-todos', [DocumentController::class, 'downloadMerged'])->name('documents.downloadAll');
Route::get('/documentos/{id}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documentos/{id}/editar', [DocumentController::class, 'edit'])->name('documents.edit');
Route::put('/documentos/{id}', [DocumentController::class, 'update'])->name('documents.update');
