<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Komponen;
use App\Models\Mesin;
use Illuminate\Http\Request;

class KomponenWebController extends Controller
{
    private function aturan(): array
    {
        return [
            'mesin_id'     => 'required|exists:mesins,id',
            'kategori_id'  => 'nullable|exists:kategoris,id',
            'nama'         => 'required|string|max:255',
            'interval_jam' => 'required|integer|min:1',
            'jam_terpakai' => 'nullable|numeric|min:0',
        ];
    }

    public function index()
    {
        $komponens = Komponen::with(['mesin:id,nama', 'kategori:id,nama'])
            ->orderBy('nama')
            ->get();

        $mesins    = Mesin::orderBy('nama')->get(['id', 'nama']);
        $kategoris = Kategori::orderBy('nama')->get(['id', 'nama']);

        return view('komponen.index', compact('komponens', 'mesins', 'kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());
        $data['jam_terpakai'] = $data['jam_terpakai'] ?? 0;

        Komponen::create($data);

        return redirect()->route('komponen.index')
            ->with('success', 'Komponen berhasil ditambahkan.');
    }

    public function update(Request $request, Komponen $komponen)
    {
        $data = $request->validate($this->aturan());
        $data['jam_terpakai'] = $data['jam_terpakai'] ?? $komponen->jam_terpakai;

        $komponen->update($data);

        return redirect()->route('komponen.index')
            ->with('success', 'Komponen berhasil diperbarui.');
    }

    public function destroy(Komponen $komponen)
    {
        $komponen->delete();

        return redirect()->route('komponen.index')
            ->with('success', 'Komponen berhasil dihapus.');
    }
}