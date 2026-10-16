<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\Friendships;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    public function store(Request $request, User $user)
    {
        $currentUser = $request->user();

        abort_if(
            $currentUser->is($user),
            422,
            'You cannot send a friend request to yourself.'
        );

        return DB::transaction(function () use ($currentUser, $user) {
            // Check both directions to prevent duplicate relationships.
            $existing = Friendships::query()
                ->where(function ($query) use ($currentUser, $user) {
                    $query->where('sender_id', $currentUser->id)
                        ->where('receiver_id', $user->id);
                })
                ->orWhere(function ($query) use ($currentUser, $user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $currentUser->id);
                })
                ->lockForUpdate()
                ->first();

            if ($existing) {
                // If they already requested you, accept their request.
                if (
                    $existing->status === 'pending'
                    && $existing->receiver_id === $currentUser->id
                ) {
                    $existing->update(['status' => 'accepted']);

                    return back()->with(
                        'success',
                        'You are now friends!'
                    );
                }

                $message = $existing->status === 'accepted'
                    ? 'You are already friends.'
                    : 'You have already sent this friend request.';

                return back()->with('success', $message);
            }

            Friendships::create([
                'sender_id' => $currentUser->id,
                'receiver_id' => $user->id,
                'status' => 'pending',
            ]);

            return back()->with(
                'success',
                'Friend request sent.'
            );
        });
    }

    public function accept(Request $request, Friendships $friendship)
    {
        abort_unless(
            $friendship->receiver_id === $request->user()->id
            && $friendship->status === 'pending',
            403
        );

        $friendship->update(['status' => 'accepted']);

        return back()->with('success', 'Friend request accepted.');
    }

    public function destroy(Request $request, Friendships $friendship)
    {
        $userId = $request->user()->id;

        abort_unless(
            $friendship->sender_id === $userId
            || $friendship->receiver_id === $userId,
            403
        );

        $wasAccepted = $friendship->status === 'accepted';

        $friendship->delete();

        return back()->with(
            'success',
            $wasAccepted
                ? 'Friend removed.'
                : 'Friend request removed.'
        );
    }

    public function requests(Request $request)
    {
        $user = $request->user();

        $incomingRequests = Friendships::query()
            ->with('sender')
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $outgoingRequests = Friendships::query()
            ->with('receiver')
            ->where('sender_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('friendships.requests', compact(
            'incomingRequests',
            'outgoingRequests'
        ));
    }

    public function friends(Request $request)
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

        return view('friends.index', compact('friends'));
    }
}
