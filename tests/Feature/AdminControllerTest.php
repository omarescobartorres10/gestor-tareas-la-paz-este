<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_users_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.users'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'usuario',
            'department' => 'IT',
            'position' => 'Developer',
        ];

        $response = $this->actingAs($admin)->post(route('admin.store-user'), $userData);

        $response->assertRedirect(route('admin.users'));
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => 'usuario',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->patch(route('admin.update-user', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'usuario',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->patch(route('admin.update-user', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'is_active' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_active' => true,
        ]);
    }
}
