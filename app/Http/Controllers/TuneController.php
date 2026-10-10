<?php

namespace App\Http\Controllers;

use App\Models\Tune;
use App\Http\Requests\StoreTuneRequest;
use App\Http\Requests\UpdateTuneRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TuneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tunes = Auth::user()->tunes()->get();

        return view('tune.index', [ 
            'tunes' => $tunes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTuneRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tune $tune)
    {
        return view('tune.show', [
            'tune' => $tune
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tune $tune)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTuneRequest $request, Tune $tune)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tune $tune)
    {
        //
    }
}
