<?php

namespace App\Http\Controllers;

use App\Models\PostSave;
use Illuminate\Http\Request;
use Throwable;

class PostSaveController extends Controller
{
    public function index()
    {
        $savedPosts = PostSave::where('user_id', auth()->id())->with('post')->get();
        return  response()->json([
            'status' => 'Sukses',
            'message' => 'Sukses mendapatkan data',
            'data' => $savedPosts,
        ], 200);
    }
    public function store($id)
    {
        try {
            $save = new PostSave;
            $save->user_id = auth()->id();
            $save->post_id = $id;
            $save->save();

            return response()->json([
                'status' => 'Sukses',
                'message' => "Berhasil Mengesave postingan dengan id $id",
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

            $postSave = PostSave::where('post_id', $id);
            $postSave->delete();

            return response()->json([
                'status' => 'Sukses',
                'message' => "Berhasil Mengunsave postingan dengan id $id",
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
