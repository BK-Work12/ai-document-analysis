<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Dashboard\ClientDashboardController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Admin\DocumentChatController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Document\DocumentUploadController;
use App\Http\Controllers\Document\DocumentMessageController;
use App\Http\Controllers\Admin\JobTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome-custom');
});

// Client Dashboard
Route::middleware(['auth', 'verified', 'role:client'])->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::post('/documents/upload', [DocumentUploadController::class, 'store'])->name('documents.store');
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{document}', [ChatController::class, 'show'])->name('chats.show');
});

// Shared authenticated routes (client & admin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/documents/{id}/download', [DocumentUploadController::class, 'download'])->name('documents.download');
    Route::get('/documents/{id}/preview', [DocumentUploadController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/chat', [DocumentMessageController::class, 'chat'])->name('documents.chat');
    Route::get('/documents/{document}/messages', [DocumentMessageController::class, 'index'])->name('documents.messages.index');
    Route::post('/documents/{document}/messages', [DocumentMessageController::class, 'store'])->name('documents.messages.store');
    Route::post('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return back();
    })->name('notifications.read');
});

// Admin Dashboard
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/clients/{user}', [AdminDashboardController::class, 'showClient'])->name('admin.clients.show');
    Route::get('/admin/clients/{user}/email-logs', [AdminDashboardController::class, 'emailLogs'])->name('admin.clients.email-logs');    Route::get('/admin/chats', [AdminChatController::class, 'list'])->name('admin.chats.list');    Route::get('/admin/clients/{user}/chats', [AdminChatController::class, 'index'])->name('admin.chats.index');
    Route::get('/admin/clients/{user}/chats/{document}', [AdminChatController::class, 'show'])->name('admin.chats.show');
    Route::patch('/admin/documents/{document}/status/{status}', [AdminDashboardController::class, 'markDocumentStatus'])->name('admin.documents.status');
    
    // Document Type Management
    Route::resource('/admin/document-types', DocumentTypeController::class, ['as' => 'admin']);
    Route::post('/admin/document-types/reorder', [DocumentTypeController::class, 'reorder'])->name('admin.document-types.reorder');
    
    // User Management
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/admin/users/{user}/data', [UserController::class, 'getUser'])->name('admin.users.data');
    Route::get('/admin/users/{user}/documents', [UserController::class, 'documents'])->name('admin.users.documents');
    Route::get('/admin/users/documents/{document}/messages', [UserController::class, 'documentMessages'])->name('admin.users.documents.messages');
    Route::post('/admin/users/documents/{document}/messages', [UserController::class, 'sendDocumentMessage'])->name('admin.users.documents.messages.send');
    
    // Settings Management
    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::patch('/admin/settings/storage', [SettingsController::class, 'updateStorage'])->name('admin.settings.updateStorage');
    Route::patch('/admin/settings/s3', [SettingsController::class, 'updateS3'])->name('admin.settings.updateS3');
    Route::patch('/admin/settings/textract', [SettingsController::class, 'updateTextract'])->name('admin.settings.updateTextract');
    Route::patch('/admin/settings/bedrock', [SettingsController::class, 'updateBedrock'])->name('admin.settings.updateBedrock');
    Route::patch('/admin/settings/ses', [SettingsController::class, 'updateSES'])->name('admin.settings.updateSES');
    
    // Document Chat (AI analysis conversation)
    Route::get('/admin/documents/chat', [DocumentChatController::class, 'index'])->name('admin.documents.chat');
    Route::get('/admin/documents/chat/users/{user}/documents', [DocumentChatController::class, 'userDocuments'])->name('admin.documents.chat.user.documents');
    Route::get('/admin/documents/{document}/conversations', [DocumentChatController::class, 'conversations'])->name('admin.documents.conversations');
    Route::post('/admin/documents/{document}/conversations', [DocumentChatController::class, 'startConversation'])->name('admin.documents.conversations.start');
    Route::get('/admin/conversations/{conversation}', [DocumentChatController::class, 'show'])->name('admin.conversations.show');
    Route::post('/admin/conversations/{conversation}/messages', [DocumentChatController::class, 'sendMessage'])->name('admin.conversations.messages.store');
    Route::post('/admin/conversations/{conversation}/messages/{message}/send-to-client', [DocumentChatController::class, 'sendMessageToClient'])->name('admin.conversations.messages.send-to-client');
    Route::patch('/admin/conversations/{conversation}/messages/{message}', [DocumentChatController::class, 'updateMessage'])->name('admin.conversations.messages.update');
    Route::patch('/admin/conversations/{conversation}', [DocumentChatController::class, 'updateStatus'])->name('admin.conversations.update');
    Route::delete('/admin/conversations/{conversation}', [DocumentChatController::class, 'destroy'])->name('admin.conversations.destroy');
    
    // Job Tracking
    Route::get('/admin/jobs', [JobTrackingController::class, 'index'])->name('admin.jobs.index');
    Route::post('/admin/jobs/{job}/retry', [JobTrackingController::class, 'retryFailed'])->name('admin.jobs.retry-failed');
    Route::post('/admin/jobs/clear-failed', [JobTrackingController::class, 'clearFailed'])->name('admin.jobs.clear-failed');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
