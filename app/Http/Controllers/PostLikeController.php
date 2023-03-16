<?php

namespace App\Http\Controllers;

use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PostLikeController extends Controller
{
    public function store($id)
    {
        try {
            $like = new PostLike();
            $like->user_id = auth()->id();
            $like->post_id = $id;
            $like->save();

            return response()->json([
                'status' => 'Sukses',
                'message' => "Anda Mengelike postingan dengan id $id",
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            // mengambil id user yang sedang login
            $userId = Auth::id();
            $like = PostLike::where('id', $id)->where('user_id', $userId)->first();
            if ($like) {
                $like->delete();
            }
            return response()->json([
                'status' => 'Sukses',
                'message' => "Anda Mengunlike postingan dengan id $id",
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
}
