<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komponen;
use Illuminate\Http\Request;

class KomponenController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Daftar komponen',
            'data'    => Komponen::with(['mesin:id,nama', 'kategori:id,nama'])->get(),
        ]);
    }

    public function show(Komponen $komponen)
    {
        return response()->json([
            'message' => 'Detail komponen',
            'data'    => $komponen->load(['mesin:id,nama', 'kategori:id,nama']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mesin_id'     => 'required|exists:mesins,id',
            'kategori_id'  => 'nullable|exists:kategoris,id',
            'nama'         => 'required|string|max:255',
            'interval_jam' => 'required|integer|min:1',
            'jam_terpakai' => 'nullable|numeric|min:0',
        ]);

        $komponen = Komponen::create($data);

        return response()->json([
            'message' => 'Komponen berhasil ditambahkan',
            'data'    => $komponen,
        ], 201);
    }

    public function update(Request $request, Komponen $komponen)
    {
        $data = $request->validate([
            'mesin_id'     => 'sometimes|exists:mesins,id',
            'kategori_id'  => 'sometimes|nullable|exists:kategoris,id', 
            'nama'         => 'sometimes|string|max:255',
            'interval_jam' => 'sometimes|integer|min:1',
            'jam_terpakai' => 'sometimes|numeric|min:0',
        ]);

        $komponen->update($data);

        return response()->json([
            'message' => 'Komponen berhasil diperbarui',
            'data'    => $komponen,
        ]);
    }

    public function destroy(Komponen $komponen)
    {
        $komponen->delete();

        return response()->json(['message' => 'Komponen berhasil dihapus']);
    }
}