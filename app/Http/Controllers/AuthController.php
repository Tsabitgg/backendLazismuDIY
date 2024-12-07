<?php

namespace App\Http\Controllers;

use App\Helpers\JWT;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register Method untuk Pengguna Biasa
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:users,name',
            'phone_number' => 'required|string|unique:users,phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Membuat pengguna baru dengan password default 'password123'
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make('password123'), // Password default
        ]);

        // Mengembalikan data pengguna tanpa token
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
    }

    // Login Method untuk Pengguna Biasa
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek kredensial
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Membuat payload untuk token
        $payload = [
            'sub' => $user->id, // Subject, biasanya ID pengguna
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'iat' => time(), // Issued At
            'exp' => time() + 3600, // Expired dalam 1 jam
        ];

        // Membuat token menggunakan helper JWT
        $key = env('JWT_SECRET', 'your-secret-key'); // Gunakan key dari file .env
        $token = JWT::encode($payload, $key);

        // Mengembalikan data pengguna dan token
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone_number' => $user->phone_number,
            ],
        ]);
    }

    // Register Method untuk Admin
    public function registerAdmin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:users,name',
            'phone_number' => 'required|string|unique:users,phone_number',
            'password' => 'required|string|min:6', // Password wajib diinput
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Membuat pengguna baru (Admin)
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password), // Password dari input
        ]);

        // Mengembalikan data pengguna tanpa token
        return response()->json([
            'message' => 'Admin registered successfully',
            'user' => $user,
        ]);
    }

    // Login Method untuk Admin
    public function loginAdmin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek kredensial
        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Membuat payload untuk token
        $payload = [
            'sub' => $user->id, // Subject, biasanya ID pengguna
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'iat' => time(), // Issued At
            'exp' => time() + 3600, // Expired dalam 1 jam
        ];

        // Membuat token menggunakan helper JWT
        $key = env('JWT_SECRET', 'your-secret-key'); // Gunakan key dari file .env
        $token = JWT::encode($payload, $key);

        // Mengembalikan data pengguna dan token
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone_number' => $user->phone_number,
            ],
        ]);
    }
}
