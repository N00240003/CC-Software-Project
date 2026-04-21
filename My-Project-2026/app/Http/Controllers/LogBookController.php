<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\SaveGame;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    // Show the logbook page
    public function index()
    {
        // Get the user's most recently updated save
        $latestSave = SaveGame::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->first();

        // Get their logbook entry if it exists
        $logbook = Logbook::where('user_id', auth()->id())->first();

        // Pull game_variables out of the save so the view can use them
        $gameVars = $latestSave ? $latestSave->game_variables : null;

        return view('logbook', compact('latestSave', 'logbook', 'gameVars'));
    }

    // Save or update reflection
    public function store(Request $request)
    {
        $data = $request->validate([
            'reflection_text' => 'required|string|max:2000',
            'private'         => 'boolean',
        ]);

        Logbook::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'reflection_text' => $data['reflection_text'],
                'private'         => $data['private'] ?? true,
            ]
        );

        return redirect()->route('logbook')->with('success', 'Reflection saved!');
    }
}
