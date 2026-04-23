<?php

namespace Tests\Feature;

use App\Models\SaveGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveGameTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper: create a logged in user ──────────────────────────────
    private function authenticatedUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    // ── Helper: sample game variables ────────────────────────────────
    private function gameVars(array $overrides = [])
    {
        return array_merge([
            'score_collaborator' => 4,
            'score_compromiser'  => 2,
            'score_avoider'      => 1,
            'score_competitor'   => 0,
            'score_accommodator' => 1,
            'choice_chapter1'    => 'Listened to both sides',
            'choice_chapter2'    => 'Proposed 50/50 split',
        ], $overrides);
    }

    /** @test */
    public function game_page_loads_successfully()
    {
        $this->authenticatedUser();
        $response = $this->get('/game');
        $response->assertStatus(200);
    }

    /** @test */
    public function saves_page_loads_successfully()
    {
        $this->authenticatedUser();
        $response = $this->get('/saves');
        $response->assertStatus(200);
    }

    /** @test */
    public function logbook_page_loads_successfully()
    {
        $this->authenticatedUser();
        $response = $this->get('/logbook');
        $response->assertStatus(200);
    }

    /** @test */
    public function player_can_save_to_a_slot()
    {
        $user = $this->authenticatedUser();

        $response = $this->postJson('/save-game', [
            'slot'           => 1,
            'chapter'        => 'Chapter1_Start',
            'game_variables' => $this->gameVars(),
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Check the row was actually written to the database
        $this->assertDatabaseHas('save_games', [
            'user_id' => $user->id,
            'slot'    => 1,
            'chapter' => 'Chapter1_Start',
        ]);
    }

    /** @test */
    public function saving_to_same_slot_overwrites_it()
    {
        $user = $this->authenticatedUser();

        // First save
        $this->postJson('/save-game', [
            'slot'           => 1,
            'chapter'        => 'Chapter1_Start',
            'game_variables' => $this->gameVars(),
        ]);

        // Overwrite with new chapter
        $this->postJson('/save-game', [
            'slot'           => 1,
            'chapter'        => 'Chapter2_Start',
            'game_variables' => $this->gameVars(),
        ]);

        // Should still be only one row for slot 1
        $this->assertEquals(1, SaveGame::where('user_id', $user->id)
            ->where('slot', 1)->count());

        // And it should have the updated chapter
        $this->assertDatabaseHas('save_games', [
            'user_id' => $user->id,
            'slot'    => 1,
            'chapter' => 'Chapter2_Start',
        ]);
    }

    /** @test */
    public function player_can_delete_a_save_slot()
    {
        $user = $this->authenticatedUser();

        $save = SaveGame::create([
            'user_id'        => $user->id,
            'slot'           => 2,
            'chapter'        => 'Chapter1_End',
            'game_variables' => $this->gameVars(),
            'resolver_type'  => 'Collaborator',
        ]);

        $response = $this->deleteJson("/save-game/{$save->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('save_games', ['id' => $save->id]);
    }

    /** @test */
    public function player_cannot_delete_another_users_save()
    {
        $this->authenticatedUser(); // logged in as user A

        // Save belongs to user B
        $otherUser = User::factory()->create();
        $save = SaveGame::create([
            'user_id'        => $otherUser->id,
            'slot'           => 1,
            'chapter'        => 'Chapter1_Start',
            'game_variables' => $this->gameVars(),
        ]);

        $response = $this->deleteJson("/save-game/{$save->id}");

        // Should be forbidden
        $response->assertStatus(403);

        // Save should still exist
        $this->assertDatabaseHas('save_games', ['id' => $save->id]);
    }

    /** @test */
    public function resolver_type_is_calculated_correctly()
    {
        $user = $this->authenticatedUser();

        // Collaborator has the highest score
        $this->postJson('/save-game', [
            'slot'           => 1,
            'chapter'        => 'Chapter2_End',
            'game_variables' => $this->gameVars([
                'score_collaborator' => 8,
                'score_compromiser'  => 3,
                'score_avoider'      => 1,
                'score_competitor'   => 2,
                'score_accommodator' => 1,
            ]),
        ]);

        $this->assertDatabaseHas('save_games', [
            'user_id'       => $user->id,
            'resolver_type' => 'Collaborator',
        ]);
    }

    /** @test */
    public function index_returns_all_five_slots()
    {
        $user = $this->authenticatedUser();

        // Only save to slot 1
        SaveGame::create([
            'user_id'        => $user->id,
            'slot'           => 1,
            'chapter'        => 'Chapter1_Start',
            'game_variables' => $this->gameVars(),
        ]);

        $response = $this->getJson('/save-game');

        $response->assertStatus(200);

        $data = $response->json();

        // All 5 slots should be present
        $this->assertCount(5, $data);

        // Slot 1 should have data
        $this->assertNotNull($data['1']);

        // Slots 2-5 should be null
        $this->assertNull($data['2']);
        $this->assertNull($data['3']);
    }

    /** @test */
    public function slot_must_be_between_1_and_5()
    {
        $this->authenticatedUser();

        $response = $this->postJson('/save-game', [
            'slot'           => 9,
            'chapter'        => 'Chapter1_Start',
            'game_variables' => $this->gameVars(),
        ]);

        // Laravel validation should reject this
        $response->assertStatus(422);
    }
}
