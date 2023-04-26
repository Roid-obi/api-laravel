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
        $posts = Post::with('tags:id,name')->with('categories:id,name')->paginate($request->input('per_page', 10));
        return response()->json([
            'posts' => $posts,
            
        ]);
    }

    /*
     * Store a newly created resource in storage.
     *
     *
     */
    public function store(Request $request, Post $post)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|min:3|unique:posts,title',
                'body' => 'required|string|max:255',
                'image' => 'nullable|image',
                'is_pinned' => 'boolean',
                // 'created_by' => auth()->user()->id
            ],[
                'title.required' => 'Title harus di isi.',
                'title.string' => 'Title harus berupa string.',
                'title.min' => 'Title harus memiliki 3 karakter atau lebih.',
                'title.unique' => 'Title Sudah di pakai.',
    
                'body.required' => 'Content harus di isi.',
                'body.string' => 'Content harus berupa string.',
                'body.max' => 'Content terlalu panjang. Maksimal 255 karakter.',
    
                // 'tag.required' => 'tag tidak boleh kosong dan pastikan anda sudah membuat tagnya',
                // 'tag.integer' => 'pastikan anda memasukan id tagnya',
            ]);
    
            // proses upload gambar
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalName();
                $destinationPath = public_path('/images');
                $image->move($destinationPath, $name);
                $validatedData['image'] = $name; // ubah bagian ini untuk menyimpan nama file ke $validatedData
            }
    
            $post = Post::create(array_merge($validatedData, ['created_by' => auth()->user()->id]));
    
            // simpan tag
            if ($request->input('tags')) {
                $post->tags()->attach($request->input('tags'));
            }

            // simpan category
            if ($request->input('categories')) {
                $post->categories()->attach($request->input('categories'));
            }
    
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
        $post = $post->with('tags:id,name')->with('categories:id,name')->find($post->id);
        $post->load('createdBy'); // muat relasi createdBy
        $comments = comment::where('post_id',$post->id)->with('user:id,name')->get();
        $post->views++;
        $post->save();
        
        return response()->json([
            'post' => $post,
            'comment' => $comments,
            'user' => $post->createdBy->name, // ambil nama user dari relasi createdBy
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
                'body' => 'nullable|string|max:255',
                'is_pinned' => 'boolean',
            ],[
                'title.string' => 'Title harus berupa string.',
                'title.min' => 'Title harus lebih dari 3 karakter atau lebih.',

                'body.string' => 'Content harus berupa string.',
                'body.max' => 'Content terlalu panjang. Maksimal 255 karakter.'
            ]);
            $post->title = $validatedData['title'] ?? $post->title;
            $post->body = $validatedData['body'] ?? $post->body;
            $post->created_by = $request->user()->id;
            $post->is_pinned = $validatedData['is_pinned'] ?? $post->is_pinned;
            $post->save();

            // Update tags
            if ($request->input('tags')) {
                $post->tags()->sync($request->input('tags'));
            } else {
                $post->tags()->detach();
            }

            // Update tags
            if ($request->input('categories')) {
                $post->categories()->sync($request->input('categories'));
            } else {
                $post->categories()->detach();
            }

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

    // muncul post berdasarkan tag
    public function postsByTag(Request $request, $tagName)
    {
        $posts = Post::with('tags:id,name')->whereHas('tags', function ($query) use ($tagName) {
            $query->where('name', $tagName);
        })->paginate($request->input('per_page', 10));

        return response()->json([
            'posts' => $posts,
        ]);
    }

    // muncul post berdasarkan category
    public function postsByCategory(Request $request, $categoryName)
    {
        $posts = Post::with('categories:id,name')->whereHas('categories', function ($query) use ($categoryName) {
            $query->where('name', $categoryName);
        })->paginate($request->input('per_page', 10));

        return response()->json([
            'posts' => $posts,
        ]);
    }

}