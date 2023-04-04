<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($postId)
    {
        $comments = Comment::where('post_id',$postId)->with('user:id,name')->paginate(10);;
        return response()->json([
            'Komentar' => $comments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $postId)
    {
        try {
            $user = auth()->user();
    
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|min:3|max:255',
            ],[
                'content.required' => 'Text harus di isi.',
                'content.string' => 'Text harus berupa string.',
                'content.min' => 'Text harus memiliki 3 karakter atau lebih.',
                'content.max' => 'Text harus harus kurang dari 255.',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }
    
            $comment = new comment([
                'content' => $request->input('content'),
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
    
            $comment->save();
    
            return response()->json([
                'message' => 'Komentar telah berhasil ditambahkan.',
                'komentar' => $comment
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $comment = comment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'content' => 'nullable|string|min:3|max:255',
            ],[
                'content.string' => 'Text harus berupa string.',
                'content.min' => 'Text harus memiliki 3 karakter atau lebih.',
                'content.max' => 'Text harus harus kurang dari 255.',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $content = $request->input('content');

            if (!is_null($content) && !empty($content)) {
                $comment->content = $content;
                $comment->save();
            }

            return response()->json([
                'message' => 'Komentar berhasil diperbarui.',
                // 'komentar' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $comment = comment::findOrFail($id);
        $comment->delete();
        return response()->json([
            'message' => 'Komentar berhasil di hapus.',
            'komentar' => $comment
        ]);
    }
}
