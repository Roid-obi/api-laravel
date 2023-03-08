<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::post('auth/register', [RegisterController::class , 'register']); // register
Route::post('auth/login', [LoginController::class, 'login']); // login



// reset password
Route::post('/password/forgot',[ResetPasswordController::class, 'token']);
Route::post('/password/reset',[ResetPasswordController::class, 'reset']);


// Detail & update profile
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfilController::class, 'index']); // Detail Profil
    Route::put('/profile', [ProfilController::class, 'update']); // update
    Route::post('/logout', [LoginController::class, 'logout']); // logout
});

//user
Route::post('/users', [UserController::class, 'create']); // create
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class , 'index']); // show all
    Route::get('/users/{id}', [UserController::class , 'show']); // show single
    Route::put('/users/{id}', [UserController::class, 'update']); // update
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // delete
});


//Post
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
});
Route::get('/post/{postId}/views', function ($postId) {
    $post = Post::findOrFail($postId);
    $view = $post->views;
    return response()->json([
        'viewer' => $view,
    ]);
});


// comment
Route::middleware('auth:sanctum')->controller(CommentController::class)->group(function () {
    Route::get('/post/{postId}/comments', 'index'); //lihat comment
    Route::post('/post/{postId}/comments', 'store'); //tambah comment
    Route::put('/comment/{id}', 'update'); //update comment
    Route::delete('/comment/{id}', 'destroy'); //hapus
});
