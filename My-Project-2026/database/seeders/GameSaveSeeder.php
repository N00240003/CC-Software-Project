<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSaveSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('save_games')->insert([
            [
                'user_id' => 1,
                'slot' => 1,
                'chapter' => 'Chapter1_Start',
                'game_variables' => json_encode([
                    "playerName" => "John",
                    "currentChapter" => 1,
                    "saveChapter" => "Chapter1_Start",
                    "score_collaborator" => 2,
                    "score_compromiser" => 1,
                    "score_avoider" => 0,
                    "score_competitor" => 0,
                    "score_peacekeeper" => 1,
                    "flag_choseDialogue" => true,
                    "flag_choseForce" => false,
                    "flag_choseWithdraw" => false,
                ]),
                'resolver_type' => 'Collaborator',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 1,
                'slot' => 2,
                'chapter' => 'Dragons_Den',
                'game_variables' => json_encode([
                    "playerName" => "John",
                    "currentChapter" => 2,
                    "saveChapter" => "Dragons_Den",
                    "score_collaborator" => 0,
                    "score_compromiser" => 2,
                    "score_avoider" => 1,
                    "score_competitor" => 3,
                    "score_peacekeeper" => 0,
                    "flag_choseDialogue" => false,
                    "flag_choseForce" => true,
                    "flag_choseWithdraw" => false,
                ]),
                'resolver_type' => 'Competitor',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 2,
                'slot' => 1,
                'chapter' => 'Chapter_1',
                'game_variables' => json_encode([
                    "playerName" => "Jane",
                    "currentChapter" => 2,
                    "saveChapter" => "Chapter_1",
                    "score_collaborator" => 1,
                    "score_compromiser" => 3,
                    "score_avoider" => 0,
                    "score_competitor" => 0,
                    "score_peacekeeper" => 2,
                    "flag_choseDialogue" => true,
                    "flag_choseForce" => false,
                    "flag_choseWithdraw" => false,
                ]),
                'resolver_type' => 'Compromiser',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 3,
                'slot' => 4,
                'chapter' => 'Dragons_Den',
                'game_variables' => json_encode([
                    "playerName" => "Cat",
                    "currentChapter" => 1,
                    "saveChapter" => "Dragons_Den",
                    "score_collaborator" => 0,
                    "score_compromiser" => 1,
                    "score_avoider" => 4,
                    "score_competitor" => 0,
                    "score_peacekeeper" => 3,
                    "flag_choseDialogue" => false,
                    "flag_choseForce" => false,
                    "flag_choseWithdraw" => true,
                ]),
                'resolver_type' => 'Avoider',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'user_id' => 4,
                'slot' => 5,
                'chapter' => 'Chapter_1',
                'game_variables' => json_encode([
                    "playerName" => "Audrey",
                    "currentChapter" => 3,
                    "saveChapter" => "Chapter_1",
                    "score_collaborator" => 3,
                    "score_compromiser" => 2,
                    "score_avoider" => 0,
                    "score_competitor" => 1,
                    "score_peacekeeper" => 2,
                    "flag_choseDialogue" => true,
                    "flag_choseForce" => false,
                    "flag_choseWithdraw" => false,
                ]),
                'resolver_type' => 'Collaborator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
