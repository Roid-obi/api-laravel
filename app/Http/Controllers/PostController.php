<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /*
     * Display a listing of the resource.
     *
     *
     */
    public function index(Request $request,Post $post) {
        $comments = comment::where('post_id',$post->id)->with('user:id,name')->get();
        $posts = Post::paginate($request->input('per_page', 10));
        return response()->json([
            'posts' => $posts,
            'comment' => $comments
            
        ]);
    }

    /*
     * Store a newly created resource in storage.
     *
     *
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|min:3|unique:posts,title',
                'body' => 'required|string|max:255',
                'tag' => 'required|integer',
                'created_by' => auth()->user()->id
            ],[
                'title.required' => 'Title harus di isi.',
                'title.string' => 'Title harus berupa string.',
                'title.min' => 'Title harus memiliki 3 karakter atau lebih.',
                'title.unique' => 'Title Sudah di pakai.',

                'body.required' => 'Content harus di isi.',
                'body.string' => 'Content harus berupa string.',
                'body.max' => 'Content terlalu panjang. Maksimal 255 karakter.',

                'tag.required' => 'tag tidak boleh kosong dan pastikan anda sudah membuat tagnya',
                'tag.integer' => 'pastikan anda memasukan id tagnya',
            ]);

            


            $post = Post::create(array_merge($validatedData, ['created_by' => auth()->user()->id]));
            return response()->json([
                'status' => 'sukses',
                'message' => 'Post Berhasil Di Buat.',
                // 'post' => $post
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
     * Display the specified resource.
     *
     *
     */
    public function show(Post $post)
    {
        $post->load('createdBy'); // muat relasi createdBy
        $comments = comment::where('post_id',$post->id)->with('user:id,name')->get();
        $post->views++;
        $post->save();
        return response()->json([
            'post' => $post,
            'comment' => $comments,
            'user' => $post->createdBy->name // ambil nama user dari relasi createdBy
        ]);
    }

    /*
     * Update the specified resource in storage.
     *
     *
     */
    public function update(Request $request, Post $post)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'nullable|string|min:3',
                'body' => 'nullable|string|max:255'
            ],[
                'title.string' => 'Title harus berupa string.',
                'title.min' => 'Title harus lebih dari 3 karakter atau lebih.',

                'body.string' => 'Content harus berupa string.',
                'body.max' => 'Content terlalu panjang. Maksimal 255 karakter.'
            ]);
            $post->title = $validatedData['title'] ?? $post->title;
            $post->body = $validatedData['body'] ?? $post->body;
            $post->created_by = $request->user()->id;
            $post->save();

            return response()->json([
                'status' => 'sukses',
                'message' => 'Post Berhasil Di Perbaharui.',
                // 'post' => $post
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();
            return response()->json([
                'errors' => $errors,
            ], 422);
        }
    }

    /*
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Post Berhasil Di Hapus.',
            'data' => $post
        ]);
    }


    // Likes
    // public function like($id, Request $request)
    // {
    //     try {
    //         $post = Post::find($id);
    //         $user = Auth::user()->id;

    //         $data = $request->all();
    //         $data['post_id'] = $post->id;
    //         $data['user_id'] = $user;

    //         PostLikes::create($data);

    //        return response()->json([
    //         "status" => "Success",
    //         "massage" => "Post berhasil di like"
    //        ], 200);
    //     } catch (\Throwable $th) {
    //         info($th);

    //         return response()->json([
    //             "status" => "Error",
    //             "massage" => "Terjadi kesalahan pada saat like post",
    //         ], 400);
    //     }
    // }

    // public function unlike($id)
    // {
    //     try {
    //         PostLikes::where('post_id', $id)->delete();
    //         return response()->json([
    //             "status" => "Success",
    //             "massage" => "Post berhasil di unlike"
    //            ], 200);

    //     } catch (\Throwable $th) {
    //         info($th);

    //         return response()->json([
    //             "status" => "Error",
    //             "massage" => "Terjadi kesalahan pada saat like post",
    //         ], 400);
    //     }

    // }




    // Saves Post
    // public function save(Request $request, $id)
    // {

    //     try {
    //         $post = Post::findOrFail($id);
    //         $user = Auth::user()->id;

    //         $data = $request->all();
    //         $data['post_id'] = $post->id;
    //         $data['user_id'] = $user;


    //         PostSave::create($data);

    //         return response()->json([
    //             "status" => "Success",
    //             "massage" => "postingan berhasil di save",
    //         ], 200);

    //     } catch (\Throwable $th) {
    //         info($th);

    //         return response()->json([
    //             "status" => "Error",
    //             "massage" => "Terjadi kesalahan pada saat sedang save post",
    //         ], 400);
    //     }
    // }

    // public function unsave($id)
    // {
    //     try {
    //         PostSave::where('post_id', $id)->delete();
    //         return response()->json([
    //             "status" => "Success",
    //             "massage" => "Post berhasil di unsave"
    //            ], 200);

    //     } catch (\Throwable $th) {
    //         info($th);

    //         return response()->json([
    //             "status" => "Error",
    //             "massage" => "Terjadi kesalahan pada saat unsave post",
    //         ], 400);
    //     }
    // }

}