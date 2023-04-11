<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\PostSaveController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
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


// register
Route::post('auth/register', [RegisterController::class , 'register']); // register
// login
Route::post('auth/login', [LoginController::class, 'login'])
    ->middleware(ThrottleRequests::class . ':5,1');
    // Dengan demikian, jika pengguna mencoba login lebih dari 5 kali dalam 1 menit, maka sistem akan memberikan response 429 Too Many Requests. Konfigurasi throttle juga dapat disesuaikan dengan kebutuhan aplikasi.


// Reset password
Route::post('/password/forgot',[ResetPasswordController::class, 'token']);
Route::post('/password/reset',[ResetPasswordController::class, 'reset'])->middleware(ThrottleRequests::class . ':5,1');


// Detail & update profile
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfilController::class, 'index']); // Detail Profil
    Route::put('/profile', [ProfilController::class, 'update']); // update
    Route::post('/logout', [LoginController::class, 'logout']); // logout
});

//user
Route::prefix('users')->middleware('auth:sanctum')->group(function() {
    Route::controller(UserController::class)->group(function () {
        Route::get('/',  'index',); //show all
        Route::post('/',  'create'); //create
        Route::get('/{id}', 'show'); //show single
        Route::put('/{id}', 'update'); //update
        Route::delete('/{id}', 'destroy'); //delete
        
    });
});
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/users', [UserController::class , 'index']); // show all
//     Route::post('/users', [UserController::class, 'create']); // create
//     Route::get('/users/{id}', [UserController::class , 'show']); // show single
//     Route::put('/users/{id}', [UserController::class, 'update']); // update
//     Route::delete('/users/{id}', [UserController::class, 'destroy']); // delete
// });







//Post
Route::get('/posts', [PostController::class , 'index']); // show all
Route::get('/posts/{post}', [PostController::class , 'show']); // show single
Route::get('/posts/tag/{tagName}', [PostController::class, 'postsByTag']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']); // create
    
    Route::put('/posts/{post}', [PostController::class, 'update']); // update
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});

Route::get('/post/{postId}/views', function ($postId) { //view post
    $post = Post::findOrFail($postId);
    $view = $post->views;
    return response()->json([
        'viewer' => $view,
    ]);
});


// comment
Route::middleware('auth:sanctum')->controller(CommentController::class)->group(function () {
    Route::get('/post/{postId}/comments', 'index'); //lihat comment
    Route::post('/post/{postId}/comments', 'store')->middleware(ThrottleRequests::class . ':5,1'); //tambah comment
    Route::put('/comment/{id}', 'update')->middleware(ThrottleRequests::class . ':5,1'); //update comment
    Route::delete('/comment/{id}', 'destroy'); //hapus
});



Route::prefix('post')->group(function () {
    Route::controller(PostLikeController::class)->group(function () {
        Route::post('/like/{post}', 'store')->middleware('auth:sanctum');
        Route::delete('/unlike/{like}', 'destroy')->middleware('auth:sanctum');
    });
    Route::controller(PostSaveController::class)->group(function () {
        Route::post('/save/{post}', 'store')->middleware('auth:sanctum');
        Route::get('/postsave', 'index')->middleware('auth:sanctum');
        Route::delete('/unsave/{save}', 'destroy')->middleware('auth:sanctum');
    });
});


Route::prefix('tag')->group(function () {
    Route::controller(TagController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/create', 'store')->middleware('auth:sanctum');
        Route::delete('/delete/{tag}', 'destroy')->middleware('auth:sanctum');
        Route::put('/update/{tag}', 'update')->middleware('auth:sanctum');
    });
});