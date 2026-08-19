<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard if authenticated, otherwise to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Auth routes
Auth::routes();

// Redirect home to dashboard
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('tickets', TicketController::class);
    
    // AJAX route for status update
    Route::post('/tickets/{ticket}/update-status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    
    // All comments page
    Route::get('/ticket-comments', [TicketCommentController::class, 'allComments'])->name('comments.all');
    
    // Ticket comments routes
    Route::prefix('tickets/{ticket}')->group(function () {
        Route::get('/comments', [TicketCommentController::class, 'index'])->name('tickets.comments.index');
        Route::post('/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store');
        Route::get('/comments/{comment}', [TicketCommentController::class, 'show'])->name('tickets.comments.show');
        Route::put('/comments/{comment}', [TicketCommentController::class, 'update'])->name('tickets.comments.update');
        Route::delete('/comments/{comment}', [TicketCommentController::class, 'destroy'])->name('tickets.comments.destroy');
    });
});
