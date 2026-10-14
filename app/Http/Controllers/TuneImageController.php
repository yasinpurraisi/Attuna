<?php

namespace App\Http\Controllers;

use App\Models\Tune;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TuneImageController extends Controller
{
    public function destroy(Tune $tune)
    {
        Gate::authorize('workWith', $tune);

        Storage::build([
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
        ])->delete($tune->image_path);

        $tune->update(['image_path' => null]);

        return back();
    }
}
