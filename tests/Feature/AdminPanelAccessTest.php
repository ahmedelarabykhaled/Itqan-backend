<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_admin_panel_outside_local_env(): void
    {
        config(['app.env' => 'production']);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }
}
