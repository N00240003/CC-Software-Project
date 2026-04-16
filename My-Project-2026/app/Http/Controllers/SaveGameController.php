<?php

namespace App\Http\Controllers;

use App\Models\SaveGame;
use Illuminate\Http\Request;

class SaveGameController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $saves = SaveGame::where('user_id', auth()->id())
            ->orderBy('slot')
            ->get()
            ->keyBy('slot');

        $slots = collect(range(1, 5))->mapWithKeys(fn($i) => [
            $i => $saves->get($i)
        ]);
        return response()->json($slots);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'slot' => 'required|integer|min:1|max:5',
            'chapter' => 'nullable|string',
            'saveData' => 'required|string',
        ]);

        \App\Models\SaveGame::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'slot' => $data['slot']
            ],
            [
                'chapter' => $data['chapter'],
                'save_data' => $data['saveData']
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Display the specified resource.
     */
    public function show(SaveGame $saveGame)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaveGame $saveGame)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaveGame $saveGame)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaveGame $saveGame)
    {
        // Make sure the save belongs to the logged in user
        if ($saveGame->user_id !== auth()->id()) {
            abort(403); // 403 = Forbidden
        }

        $saveGame->delete();

        return response()->json(['success' => true]);
    }

    private function calculateResolverType(array $vars): string
    // Private means this method can only be called from inside this controller
    // (it's a helper, not route)
    {
        $scores = [
            'Collaborator' => $vars['score_collaborator'] ?? 0,
            //?? 0 means "use 0 if this key doesn't exist" 
            // -> so if a score variable is missing it won't crash.
            'Compromiser'  => $vars['score_compromiser']  ?? 0,
            'Avoider'      => $vars['score_avoider']      ?? 0,
            'Competitor'   => $vars['score_competitor']   ?? 0,
            'Accommodator' => $vars['score_accommodator'] ?? 0,
        ];

        // arsort sorts highest to lowest, array_key_first gets the top one
        arsort($scores);
        return array_key_first($scores);
    }
}
