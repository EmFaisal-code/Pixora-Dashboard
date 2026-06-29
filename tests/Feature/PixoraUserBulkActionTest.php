<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class PixoraUserBulkActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test non-authenticated users cannot access bulk delete.
     */
    public function test_guest_cannot_bulk_delete()
    {
        $response = $this->postJson('/admin/pixora-users/bulk-delete', [
            '_method' => 'DELETE',
            'usernames' => ['testuser1']
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test non-authenticated users cannot access bulk ban.
     */
    public function test_guest_cannot_bulk_ban()
    {
        $response = $this->postJson('/admin/pixora-users/bulk-ban', [
            'usernames' => ['testuser1']
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated users can bulk ban (mocking HTTP).
     */
    public function test_admin_can_bulk_ban()
    {
        $admin = User::factory()->create();

        Http::fake([
            'movecexnjyeaipkklijv.supabase.co/*' => Http::response(['success' => true], 200)
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/pixora-users/bulk-ban', [
            'usernames' => ['testuser1', 'testuser2'],
            'reason' => 'Spam accounts'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
