<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiBarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();
        if($barang) {
            return response()->json([
                'status' => true,
                'data' => $barang
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'data' => []
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'nama' => 'required',
            'kategori' => 'required',
            'lokasi' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'kategori.required' => 'Kategori wajib diisi',
            'lokasi.required' => 'Lokasi wajib diisi',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus angka',
            'stok.required' => 'Stok wajib diisi',
            'stok.numeric' => 'Stok harus angka',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($request->stok <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Stok harus lebih dari 0'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $value = [
            'kode' => Str::random(6),
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'harga' => $request->harga,
            'stok' => $request->stok,
        ];

        $barang = Barang::create($value);
        if($barang) {
            return response()->json([
                'status' => true,
                'data' => $barang,
                'message' => 'Barang berhasil disimpan'
            ], Response::HTTP_CREATED);
        }

        return response()->json([
            'status' => false,
            'message' => 'Barang gagal disimpan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function show($id)
    {
        $data = Barang::find($id);
        if($data) {
            return response()->json([
                'status' => true,
                'data' => $data
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data barang tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(request()->all(), [
            'nama' => 'required',
            'kategori' => 'required',
            'lokasi' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'lokasi.required' => 'Lokasi wajib diisi',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus angka',
            'stok.required' => 'Stok wajib diisi',
            'stok.numeric' => 'Stok harus angka',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($request->stok <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Stok harus lebih dari 0'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $value = [
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'harga' => $request->harga,
            'stok' => $request->stok,
        ];

        $barang = Barang::find($id);
        $update = $barang->update($value);
        if($update) {
            return response()->json([
                'status' => true,
                'data' => $barang,
                'message' => 'Barang berhasil diupdate'
            ], Response::HTTP_CREATED);
        }

        return response()->json([
            'status' => false,
            'message' => 'Barang gagal diupdate / barang tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function delete($id)
    {
        $data = Barang::find($id);
        if($data) {
            $delete = $data->delete();
            if($delete) {
                return response()->json([
                    'status' => true,
                    'message' => 'Barang berhasil dihapus'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status' => false,
                'message' => 'Barang gagal dihapus'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'status' => false,
            'message' => 'Barang tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
