<?php

namespace App\Http\Controllers;

use App\Models\Friendships;
use App\Models\User;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
        ]);

        $currentUser = $request->user();
        $search = trim($validated['q'] ?? '');

        $users = User::query()
            ->where('id', '!=', $currentUser->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $friendshipsByUserId = Friendships::query()
            ->where(function ($query) use ($currentUser) {
                $query->where('sender_id', $currentUser->id)
                    ->orWhere('receiver_id', $currentUser->id);
            })
            ->get()
            ->keyBy(function ($friendship) use ($currentUser) {
                return (int) $friendship->sender_id === (int) $currentUser->id
                    ? $friendship->receiver_id
                    : $friendship->sender_id;
            });

        return view('users.index', compact(
            'users',
            'search',
            'friendshipsByUserId'
        ));
    }

    public function show(Request $request, User $user)
    {
        abort_if($request->user()->is($user), 404);

        $currentUser = $request->user();

        $friendship = Friendships::query()
            ->where(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $currentUser->id)
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id);
            })
            ->first();

        return view('users.show', compact('user', 'friendship'));
    }
}