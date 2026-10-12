<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTuneRequest;
use App\Http\Requests\UpdateTuneRequest;
use App\Models\Tune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TuneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    public function store(StoreTuneRequest $request)
    {
        // Auth::user()->tunes()->create($request->safe()->except('tasks'));
        $tune = Auth::user()->tunes()->create($request->safe()->except('tasks'));
        $tune->tasks()->createMany(
        collect($request->tasks)->map(
            fn($task)=>['description'=>$task])
        );

        return to_route('tunes.index')->with('success', 'Tune created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tune $tune)
    {
        return view('tune.show', [
            'tune' => $tune,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tune $tune): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTuneRequest $request, Tune $tune): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tune $tune)
    {
        //!Authorize that is allowed.
        $tune->delete();
        return to_route('tunes.index');
    }
}
