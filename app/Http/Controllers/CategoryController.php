<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CategoryController extends Controller
{
    
    public function index()
    {
        try {

            return response()->json([
                "status" => "success",
                "massage" => "Berhasil melihat semua data Categories",
                "data" => Category::all()
            ], 200);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                "status" => "error",
                "massage" => "Error pada Categories"
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

            Category::create($input);

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil membuat Category baru',
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
            $category = Category::find($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json([
                'status' => 'Sukses',
                'message' => 'Berhasil Mengedit Category',
            ], 201);
        } catch (Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'Failed',
                'message' => 'Terjadi Kesalahan Sistem Silahkan Coba Beberapa Saat Lagi'
            ]);
        }
    }
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'status' => 'Sukses',
                'message' => 'sukses menghapus category'
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
