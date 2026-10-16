<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash' ,Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect('/')->with('success', 'Account created successfully!');
    }
}
