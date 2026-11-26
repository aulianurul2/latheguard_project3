<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Daftar pengguna',
            'data'    => User::select('id', 'name', 'email', 'role')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:owner,teknisi',
        ]);

        $user = User::create($data);

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan',
            'data'    => $user->only('id', 'name', 'email', 'role'),
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|string|min:8',
            'role'     => 'sometimes|in:owner,teknisi',
        ]);

        $user->update($data);

        // Kalau password atau role benar-benar berubah, login lama dicabut
        if ($user->wasChanged(['password', 'role'])) {
            $tokenSaatIni = $request->user()->currentAccessToken();

            // Kalau owner mengubah akunnya sendiri, token yang sedang dipakai dipertahankan
            $kecuali = ($request->user()->id === $user->id && $tokenSaatIni instanceof PersonalAccessToken)
                ? $tokenSaatIni->id
                : null;

            $this->cabutAkses($user, $kecuali);
        }

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui',
            'data'    => $user->only('id', 'name', 'email', 'role'),
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri'], 422);
        }

        $this->cabutAkses($user);
        $user->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }

    // Mencabut semua token (Flutter, Postman) dan sesi web milik pengguna
    private function cabutAkses(User $user, ?int $kecualiTokenId = null): void
    {
        $tokens = $user->tokens();

        if ($kecualiTokenId) {
            $tokens->where('id', '!=', $kecualiTokenId);
        }

        $tokens->delete();

        DB::table('sessions')->where('user_id', $user->id)->delete();
    }
}