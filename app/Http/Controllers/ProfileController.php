<?php

namespace App\Http\Controllers;

use App\Models\Friendships;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $friendships = Friendships::query()
            ->with(['sender', 'receiver'])
            ->where('status', 'accepted')
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->latest()
            ->get();

        $friends = $friendships->map(function ($friendship) use ($user) {
            $friend = (int) $friendship->sender_id === (int) $user->id
                ? $friendship->receiver
                : $friendship->sender;

            if ($friend) {
                $friend->setAttribute('friendship_id', $friendship->id);
            }

            return $friend;
        })->filter()->values();

        // Exclude users with whom a friendship or request already exists.
        $relatedUserIds = Friendships::query()
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->get()
            ->map(fn ($friendship) =>
                (int) $friendship->sender_id === (int) $user->id
                    ? $friendship->receiver_id
                    : $friendship->sender_id
            );

        $suggestedUsers = User::query()
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $relatedUserIds)
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('profile.index', compact(
            'user',
            'friends',
            'suggestedUsers'
        ));
    }

    public function update(Request $request)
    {   
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash' ,Rule::unique('users', 'username')->ignore($user->id)],
            'avatar' => ['required', Rule::in(array_merge(['default.svg'], array_map(fn ($number) => $number . '.svg',range(1, 29)))),],
        ]);

        $user->update($validated);

        return redirect('/profile')->with('success', 'Profile updated successfully!');

        
    }

    public function destroy(Request $request)
    {
        $request->validate([
                    'password' => ['required', 'current_password'],
                ]);

        $user = $request->user();

        // Log out before deleting the account.
        Auth::logout();

        $user->delete();

        // Invalidate the session.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Your account has been deleted.');
    }
}
