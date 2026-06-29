<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class PixoraUserRefreshTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the user index page loads and contains the refresh button.
     */
    public function test_pixora_users_page_contains_refresh_button(): void
    {
        // Create an admin user to bypass authentication
        $admin = User::factory()->create();

        // Acting as the admin, visit the pixora-users page
        $response = $this->actingAs($admin)->get('/admin/pixora-users');

        // Assert the page loads successfully
        $response->assertStatus(200);

        // Assert the page contains the Refresh Data button attribute string
        $response->assertSee('Refresh Data');
        
        // Assert the page contains the Alpine JS dispatcher for trigger-refresh
        $response->assertSee('trigger-refresh');
        
        // Assert the page contains the refresh-cw icon
        $response->assertSee('refresh-cw');
    }
}
