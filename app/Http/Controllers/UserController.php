<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
// Menampilkan daftar pengguna dengan pagination dan search
public function index(Request $request)
{
    // Ambil query search dari request, jika ada
    $search = $request->query('search');

    // Ambil jumlah data per halaman dari query, jika tidak ada gunakan default 10
    $perPage = $request->query('per_page', 10);

    // Jika ada search, filter berdasarkan username atau phone_number
    if ($search) {
        $users = User::where('username', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->paginate($perPage);
    } else {
        // Jika tidak ada search, tampilkan semua pengguna dengan pagination
        $users = User::paginate($perPage);
    }

    // Kembalikan hasil dengan format JSON
    return response()->json($users);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }



// Menampilkan data pengguna berdasarkan ID
public function show($id)
{
    // Cari pengguna berdasarkan ID
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Kembalikan data pengguna dalam format JSON
    return response()->json($user);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
