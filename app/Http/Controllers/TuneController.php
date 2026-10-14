<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateTune;
use App\Actions\UpdateTune;
use App\Http\Requests\TuneRequest;
use App\Models\Tune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TuneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /** @var User */
    public function index(Request $request)
    {
        $tunes = Auth::user()->tunes()
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('tune.index', [
            'tunes' => $tunes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TuneRequest $request, CreateTune $action)
    {
        // // Auth::user()->tunes()->create($request->safe()->except('tasks'));
        // $tune = Auth::user()->tunes()->create($request->safe()->except('tasks', 'image'));
        // $tune->tasks()->createMany(
        //     collect($request->tasks)->map(
        //         fn ($task) => ['description' => $task])
        // );

        // if ($request->hasFile('image')) {
        //     $imagePath = $request->file('image')->store('tunes', 'public');
        //     $tune->update([
        //         'image_path' => $imagePath,
        //     ]);
        // }
        $action->handle($request->safe()->all());

        return to_route('tunes.index')->with('success', 'Tune created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tune $tune)
    {
        Gate::authorize('workWith', $tune);

        return view('tune.show', [
            'tune' => $tune,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tune $tune): void
    {
        Gate::authorize('workWith', $tune);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TuneRequest $request, Tune $tune, UpdateTune $action)
    {
        Gate::authorize('workWith', $tune);

        $action->handle($request->safe()->all(), $tune);

        return back()->with('success', 'Tune Updated!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tune $tune)
    {
        Gate::authorize('workWith', $tune);
        $tune->delete();

        return to_route('tunes.index');
    }
}
