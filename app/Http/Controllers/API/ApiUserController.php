<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiUserController extends Controller
{
    public function index()
    {
        $data = User::all();
        if($data) {
            return response()->json([
                'status' => true,
                'data' => $data
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data user tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function show($id)
    {
        $data = User::find($id);
        if($data) {
            return response()->json([
                'status' => true,
                'data' => $data
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data user tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(request()->all(), [
            'nama' => 'required',
            'password' => 'required',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $value = [
            'nama' => $request->nama,
            'password' => Hash::make($request->password),
        ];

        $user = User::find($id);
        $update = $user->update($value);
        if($update) {
            return response()->json([
                'status' => true,
                'data' => $update,
                'message' => 'Akun berhasil diupdate',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Akun gagal diupdate / akun tidak ditemukan',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function delete($id)
    {
        $data = User::find($id);
        $delete = $data->delete();
        if($delete) {
            return response()->json([
                'status' => true,
                'data' => $delete,
                'message' => 'Akun berhasil dihapus',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Akun gagal dihapus / akun tidak ditemukan',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }



}
