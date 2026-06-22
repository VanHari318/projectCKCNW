<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     */
    public function show()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user->isStudent()) {
            $classrooms = $user->classrooms()->get();
            $courses = $user->courses()->get();

            return view('profile.show', compact('user', 'classrooms', 'courses'));
        }

        if ($user->isTeacher()) {
            $classroom = $user->classroom; // teacher's classroom (hasOne)
            $courses = $classroom ? $classroom->courses()->get() : collect();

            return view('profile.show', compact('user', 'classroom', 'courses'));
        }

        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the authenticated user's profile.
     */
    public function edit()
    {
        /** @var User|null $user */
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile (name, email, password).
     */
    public function update(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $data = $request->validate($rules);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Cập nhật thông tin thành công.');
    }
}
