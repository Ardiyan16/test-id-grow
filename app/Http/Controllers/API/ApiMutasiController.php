<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\User;
use App\Models\Mutasi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiMutasiController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    public function index()
    {
        $mutasi = DB::table('mutasis')
        ->select('mutasis.*', 'users.nama as nama_user', 'barangs.nama as nama_barang')
        ->leftJoin('users', 'mutasis.user_id', '=', 'users.id')
        ->leftJoin('barangs', 'mutasis.kode_produk', '=', 'barangs.kode')
        ->orderBy('mutasis.tanggal', 'desc')
        ->get();

        if($mutasi) {
            return response()->json([
                'status' => true,
                'data' => $mutasi
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'data' => []
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_produk' => 'required',
            'user_id' => 'required',
            'tanggal' => 'required',
            'jenis_mutasi' => 'required|in:masuk,keluar',
            'jumlah' => 'required',
        ], [
            'kode_produk.required' => 'Kode produk wajib diisi',
            'user_id.required' => 'User wajib diisi',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jenis_mutasi.required' => 'Jenis mutasi wajib diisi',
            'jenis_mutasi.in' => 'Jenis mutasi harus masuk atau keluar',
            'jumlah.required' => 'Jumlah wajib diisi',
        ]);


        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $barang  = Barang::where('kode', $request->kode_produk);
        $data_barang = $barang->first();
        if($request->jenis_mutasi == 'keluar') {
            if($request->jumlah > $data_barang->stok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Stok tidak mencukupi',
                ]);
            }
        }

        $value = [
            'kode_produk' => $request->kode_produk,
            'user_id' => $request->user_id,
            'tanggal' => $request->tanggal,
            'jenis_mutasi' => $request->jenis_mutasi,
            'jumlah' => $request->jumlah,
        ];

        $mutasi = Mutasi::create($value);
        if($mutasi) {
            if($request->jenis_mutasi == 'masuk') {
                $stok = $data_barang->stok + $request->jumlah;
                $barang->update(['stok' => $stok]);
            } else {
                $stok = $data_barang->stok - $request->jumlah;
                $barang->update(['stok' => $stok]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Data mutasi berhasil ditambahkan'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data mutasi gagal ditambahkan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_produk' => 'required',
            'user_id' => 'required',
            'tanggal' => 'required',
            'jenis_mutasi' => 'required|in:masuk,keluar',
            'jumlah' => 'required',
        ], [
            'kode_produk.required' => 'Kode produk wajib diisi',
            'user_id.required' => 'User wajib diisi',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jenis_mutasi.required' => 'Jenis mutasi wajib diisi',
            'jenis_mutasi.in' => 'Jenis mutasi harus masuk atau keluar',
            'jumlah.required' => 'Jumlah wajib diisi',
        ]);


        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $barang  = Barang::where('kode', $request->kode_produk);
        $data_barang = $barang->first();
        if($request->jenis_mutasi == 'keluar') {
            if($request->jumlah > $data_barang->stok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Stok tidak mencukupi',
                ]);
            }
        }

        $value = [
            'kode_produk' => $request->kode_produk,
            'user_id' => $request->user_id,
            'tanggal' => $request->tanggal,
            'jenis_mutasi' => $request->jenis_mutasi,
            'jumlah' => $request->jumlah,
        ];

        $data_mutasi = Mutasi::find($id);
        $stok = '';
        if($data_mutasi->jumlah != $request->jumlah) {
            if($request->jenis_mutasi == 'masuk') {
                $stok = $data_barang->stok + $request->jumlah - $data_mutasi->jumlah;
            } else {
                $stok = $data_barang->stok - $request->jumlah - $data_mutasi->jumlah;
            }
        } else {
            if($request->jenis_mutasi == 'keluar') {
                $stok = $data_barang->stok - $request->jumlah;
            }
        }

        $mutasi = Mutasi::find($id);
        $update = $mutasi->update($value);
        if($update) {
            $barang->update(['stok' => $stok]);
            return response()->json([
                'status' => true,
                'message' => 'Data mutasi berhasil diupdate'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data mutasi gagal diupdate'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function delete($id)
    {
        $data = Mutasi::find($id);
        if($data) {
            $barang = Barang::where('kode', $data->kode_produk);
            $data_barang = $barang->first();
            $stok = '';
            if($data->jenis_mutasi == 'masuk') {
                $stok = $data_barang->stok - $data->jumlah;
            } else {
                $stok = $data_barang->stok + $data->jumlah;
            }
            $delete = $data->delete();
            if($delete) {
                $barang->update(['stok' => $stok]);
                return response()->json([
                    'status' => true,
                    'message' => 'Mutasi berhasil dihapus'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status' => false,
                'message' => 'Mutasi gagal dihapus'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'status' => false,
            'message' => 'Mutasi tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function history_mutasi_user($id)
    {
        $user = User::find($id);
        $data = Mutasi::where('user_id', $id)->get();
        if($data) {
            return response()->json([
                'status' => true,
                'nama_user' => $user->nama,
                'data' => $data
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data mutasi user tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function history_mutasi_barang($kode)
    {
        $barang = Barang::where('kode', $kode)->first();
        $data = Mutasi::where('kode_produk', $kode)->get();
        if($data) {
            return response()->json([
                'status' => true,
                'nama_barang' => $barang->nama,
                'kode_barang' => $barang->kode,
                'data' => $data
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data mutasi barang tidak ditemukan'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
