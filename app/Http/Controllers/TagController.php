<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class TagController extends Controller
{

    public function index()
    {
        try {

            return response()->json([
                "status" => "success",
                "massage" => "Berhasil melihat semua data tags",
                "data" => Tag::all()
            ], 200);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                "status" => "error",
                "massage" => "Error pada Tags"
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // memvalidasi inputan post
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
        ], [
            'name.required' => 'name tidak boleh kosong',
            'name.string' => 'name harus bernilai string',

            'description.required' => 'description tidak boleh kosong',
            'description.string' => 'description harus bernilai string'
        ]);

        // mengecek ketika terjadi error saat input data
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Data yang anda berikan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $input = [
                'name' => $request->name,
                'created_by' => Auth::user()->name,
                'description' => $request->description,

            ];

            Tag::create($input);

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil membuat Tag baru',
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],

        ], [
            'name.required' => 'name tidak boleh kosong',
            'name.string' => 'name harus bernilai string',

            'description.required' => 'description tidak boleh kosong',
            'description.string' => 'description harus bernilai string',
        ]);

        // mengecek ketika terjadi error saat input data
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Data yang anda berikan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $tag = Tag::find($id);
            $tag->name = $request->name;
            $tag->description = $request->description;
            $tag->save();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil Mengedit Tag',
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'sukses menghapus tag'
            ], 200);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
}
