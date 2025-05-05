<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $email = strtolower($request->email);
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not found.'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid password.'], 401);
        }

        $token = $user->createToken($user->name . ' auth_token')->plainTextToken;

        return response()->json([
            'success' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }
    public function register(RegisterRequest $request)
    {
        $userEmail = User::where('email', $request->email)->first();
        if ($userEmail) {
            return response()->json(['error' => 'Email already exists.'], 409);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken($user->name . ' auth_token')->plainTextToken;

        return response()->json([
            'success' => 'Registration successful.',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout successful.'], 200);
    }
    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'Profile fetched successfully.',
            'data' => $request->user()
        ], 200);
    }
    public function updateProfile(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            if ($request->has('name')) {
                $user->name = $validatedData['name'];
            }

            if ($request->has('email') && $request->email !== $user->email) {
                $existingUser = User::where('email', $request->email)->first();
                if ($existingUser) {
                    return response()->json(['error' => 'Email already exists.'], 409);
                }
                $user->email = Str::lower($validatedData['email']);
            }

            if ($request->hasFile('image')) {
                $storage = Storage::disk('public');

                if ($user->image && $storage->exists($user->image)) {
                    $storage->delete($user->image);
                }

                $imageName = 'profile/' . Str::random(32) . "." . $request->image->getClientOriginalExtension();
                $storage->put($imageName, file_get_contents($request->image->getRealPath()));

                $user->image = $imageName;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'user' => $user
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed.',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Something went wrong.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        $user->delete();
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => 'Account deleted successfully.'
        ], 200);
    }
    public function getAllUsers(Request $request)
    {
        $users = User::where('role', 'user')->get();

        return response()->json($users, 200);
    }
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->delete();

        return response()->json(['success' => 'User deleted successfully.'], 200);
    }
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found.'], 404);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $code,
                'created_at' => Carbon::now(),
            ]
        );

        Mail::send('email.password_reset', ['code' => $code], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Your Password Reset Code');
        });

        return response()->json(['success' => 'A 6-digit code has been sent to your email.'], 200);
    }
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset) {
            return response()->json(['error' => 'Invalid or expired code.'], 400);
        }

        $expirationTime = Carbon::parse($reset->created_at)->addMinutes(10);
        if (Carbon::now()->gt($expirationTime)) {
            return response()->json(['error' => 'The code has expired.'], 400);
        }

        return response()->json(['success' => 'Code verified successfully.'], 200);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|confirmed|min:8',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset) {
            return response()->json(['error' => 'Invalid or expired code.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => 'Password reset successfully.'], 200);
    }
}