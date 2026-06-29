<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class PixoraUserSortingSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sorting state persists in session.
     */
    public function test_sorting_state_persists()
    {
        $admin = User::factory()->create();

        // Visit with sorting params
        $response1 = $this->actingAs($admin)->get('/admin/pixora-users?sort=username&dir=asc');
        $response1->assertStatus(200);
        
        // Check session
        $this->assertEquals('username', session('pixora_users_sort'));
        $this->assertEquals('asc', session('pixora_users_dir'));

        // Visit without sorting params
        $response2 = $this->actingAs($admin)->get('/admin/pixora-users');
        $response2->assertStatus(200);

        // Check if the session is still applied to the view
        // The controller passes $sort and $dir to the view
        $response2->assertViewHas('sort', 'username');
        $response2->assertViewHas('dir', 'asc');
    }
}
