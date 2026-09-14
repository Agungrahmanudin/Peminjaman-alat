<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|unique:kategori,nama_kategori|max:100',
            'deskripsi' => 'nullable|max:500',
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'kategori' => $kategori,
            'message' => 'Kategori berhasil ditambahkan'
        ]);
    }
}