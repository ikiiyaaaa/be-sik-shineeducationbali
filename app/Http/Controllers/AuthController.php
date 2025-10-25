<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Proses login (mirip Breeze)
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        if ($user->status !== 'Aktif') {
            return response()->json(['message' => 'Akun Anda tidak aktif.'], 403);
        }
        $token = $user->createToken('auth-token', ['*'], Carbon::now()->addDay())->plainTextToken; // Token valid 24 jam

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'message' => 'Login berhasil',
        ], 200);
    }

    // Proses registrasi (mirip Breeze)
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'Aktif',
        ]);

        $user->assignRole('Karyawan');

        $token = $user->createToken('auth-token', ['*'], Carbon::now()->addDay())->plainTextToken;
        // Token valid 24 jam

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'message' => 'Registrasi berhasil',
        ], 201);
    }

    // Proses logout (mirip Breeze)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(
            [
                'message' => 'Logout berhasil',
            ],
            204
        ); // No content, seperti Breeze
    }

    // Mengirim email reset password (mirip Breeze)
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)], 200);
    }

    // Proses reset password (mirip Breeze)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)], 200);
    }
}