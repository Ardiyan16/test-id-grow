<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $value = [
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ];

        $user = User::create($value);
        if($user) {
            return response()->json([
                'status' => true,
                'data' => $user,
                'message' => 'Akun berhasil dibuat',
            ], Response::HTTP_CREATED);
        }

        return response()->json([
            'status' => false,
            'message' => 'Akun gagal dibuat',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'password.required' => 'Password wajib diisi'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credentials = $request->only('email', 'password');
        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response([
                'status' => false,
                'message' => 'Email atau password salah!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response([
            'status' => true,
            'token' => $token,
            'user' => auth()->guard('api')->user(),
            'message' => 'Login berhasil'
        ], Response::HTTP_OK);

    }
}
