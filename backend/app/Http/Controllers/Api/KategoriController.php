<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Daftar kategori',
            'data'    => Kategori::withCount('komponens')->get(),
        ]);
    }

    public function show(Kategori $kategori)
    {
        return response()->json([
            'message' => 'Detail kategori',
            'data'    => $kategori->load('komponens'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255|unique:kategoris,nama',
            'deskripsi' => 'nullable|string',
        ]);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => Kategori::create($data),
        ], 201);
    }

    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate([
            'nama'      => 'sometimes|string|max:255|unique:kategoris,nama,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($data);

        return response()->json([
            'message' => 'Kategori berhasil diperbarui',
            'data'    => $kategori,
        ]);
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->komponens()->exists()) {
            return response()->json([
                'message' => 'Kategori masih dipakai komponen, tidak dapat dihapus',
            ], 422);
        }

        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}