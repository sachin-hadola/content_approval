<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebPostController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [WebPostController::class, 'index'])->name('dashboard');
    // Author Routes
    Route::get('/posts/create', [WebPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [WebPostController::class, 'store'])->name('posts.store');
    
    // View Posts (Author, Manager, Admin)
    Route::get('/posts/{post}', [WebPostController::class, 'show'])->name('posts.show');
    
    Route::get('/posts/{post}/edit', [WebPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [WebPostController::class, 'update'])->name('posts.update');
    
    // Manager/Admin Routes
    Route::post('/posts/{post}/approve', [WebPostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/reject', [WebPostController::class, 'reject'])->name('posts.reject');
    
    // Admin only Route
    Route::delete('/posts/{post}', [WebPostController::class, 'destroy'])->name('posts.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
