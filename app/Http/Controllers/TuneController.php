<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateTune;
use App\Actions\UpdateTune;
use App\Http\Requests\TuneRequest;
use App\Models\Friendships;
use App\Models\Tune;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TuneController extends Controller
{
    /**
     * Display owned and shared tunes.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');

        abort_unless(
            in_array($filter, ['all', 'mine', 'shared'], true),
            404
        );

        $tunes = Tune::query()
            ->with(['user'])
            ->when($filter === 'mine', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when($filter === 'shared', function (Builder $query) use ($user) {
                $query->whereHas('sharedWith', function (Builder $sharedQuery) use ($user) {
                    $sharedQuery->where('users.id', $user->id);
                })->where('user_id', '!=', $user->id);
            })
            ->when($filter === 'all', function (Builder $query) use ($user) {
                $query->where(function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('sharedWith', function (Builder $sharedQuery) use ($user) {
                            $sharedQuery->where('users.id', $user->id);
                        });
                });
            })
            ->when($request->filled('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->query('status'));
            })
            ->latest()
            ->get();

        $friends = $this->acceptedFriends($request);

        return view('tune.index', compact('tunes', 'friends', 'filter'));
    }

    /**
     * The modal is on the index page, so no separate create view is required.
     */
    public function create(): \Illuminate\Http\RedirectResponse
    {
        return to_route('tunes.index');
    }

    /**
     * Store a newly created tune.
     */
    public function store(
        TuneRequest $request,
        CreateTune $action
    ): RedirectResponse {
        $sharedFriendIds = $this->validatedSharedFriendIds($request);

        $tune = DB::transaction(function () use (
            $request,
            $action,
            $sharedFriendIds
        ): Tune {
            $attributes = $request->safe()->except('shared_with');

            $tune = $action->handle($attributes);

            $tune->sharedWith()->sync($sharedFriendIds);

            return $tune;
        });

        return to_route('tunes.show', $tune)
            ->with('success', 'Tune created successfully!');
    }
    /**
     * Display a tune to its owner or a user it has been shared with.
     */
    public function show(Request $request, Tune $tune)
    {
        Gate::authorize('workWith', $tune);

        $tune->load(['user', 'sharedWith', 'tasks']);

        $isOwner = (int) $tune->user_id === (int) $request->user()->id;

        $friends = $isOwner
            ? $this->acceptedFriends($request)
            : collect();

        return view('tune.show', compact('tune', 'isOwner', 'friends'));
    }

    /**
     * The edit modal is displayed on the tune detail page.
     */
    public function edit(Tune $tune): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('workWith', $tune);

        return to_route('tunes.show', $tune);
    }

    /**
     * Update a tune and its sharing permissions.
     */
    public function update(
        TuneRequest $request,
        Tune $tune,
        UpdateTune $action
    ): RedirectResponse {
        Gate::authorize('workWith', $tune);

        abort_unless(
            (int) $tune->user_id === (int) $request->user()->id,
            403
        );

        $sharedFriendIds = $this->validatedSharedFriendIds($request);

        DB::transaction(function () use (
            $request,
            $action,
            $tune,
            $sharedFriendIds
        ): void {
            $attributes = $request->safe()->except('shared_with');

            $action->handle($attributes, $tune);

            $tune->sharedWith()->sync($sharedFriendIds);
        });

        return to_route('tunes.show', $tune)
            ->with('success', 'Tune updated successfully!');
    }

    /**
     * Delete a tune. Only its owner may delete it.
     */
    public function destroy(Request $request, Tune $tune)
    {
        Gate::authorize('workWith', $tune);

        abort_unless(
            (int) $tune->user_id === (int) $request->user()->id,
            403
        );

        $tune->delete();

        return to_route('tunes.index')->with('success', 'Tune deleted.');
    }

    /**
     * Get accepted friends as User models.
     */
    private function acceptedFriends(Request $request): SupportCollection
    {
        $user = $request->user();

        $friendships = Friendships::query()
            ->with(['sender', 'receiver'])
            ->where('status', 'accepted')
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->get();

        return $friendships
            ->map(function ($friendship) use ($user) {
                return (int) $friendship->sender_id === (int) $user->id
                    ? $friendship->receiver
                    : $friendship->sender;
            })
            ->filter()
            ->values();
    }


    /**
     * Validate that every selected recipient is currently an accepted friend.
     *
     * @return array<int, int>
     */
    private function validatedSharedFriendIds(Request $request): array
    {
        $friendIds = $this->acceptedFriends($request)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $validated = $request->validate([
            'shared_with' => ['sometimes', 'array'],
            'shared_with.*' => [
                'required',
                'integer',
                'distinct',
                Rule::in($friendIds),
            ],
        ]);

        return array_map('intval', $validated['shared_with'] ?? []);
    }

}
