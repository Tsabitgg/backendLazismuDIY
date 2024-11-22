<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register Method
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:users,name',
            'phone_number' => 'required|string|unique:users,phone_number',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Membuat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        // Membuat token untuk pengguna baru
        $token = $user->createToken('lazismudiy')->plainTextToken;

        // Mengembalikan data pengguna dan token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Login Method
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek kredensial
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Membuat token baru
        $token = $user->createToken('lazismudiy')->plainTextToken;

        // Mengembalikan data pengguna dan token
        return response()->json([
            'token' => $token,
        ]);
    }

        // Register Method
    public function registerAdmin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:users,name',
            'phone_number' => 'required|string|unique:users,phone_number',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Membuat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'email' => $request->email,
        ]);

        // Membuat token untuk pengguna baru
        $token = $user->createToken('admlazismudiyqwerty')->plainTextToken;

        // Mengembalikan data pengguna dan token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Login Method
    public function loginAdmin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek kredensial
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Membuat token baru
        $token = $user->createToken('admlazismudiyqwerty')->plainTextToken;

        // Mengembalikan data pengguna dan token
        return response()->json([
            'token' => $token,
        ]);
    }
}
