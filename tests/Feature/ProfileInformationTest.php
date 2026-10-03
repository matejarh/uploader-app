<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_information_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->put('/user/profile-information', [
            'name' => 'Test Name',
            'company_name' => 'Example Company',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test Name', $user->fresh()->name);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }

    public function test_email_change_does_not_crash_and_resets_verification(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'company_name' => 'Example Company',
        ]);

        $this->actingAs($user);

        $response = $this->put('/user/profile-information', [
            'name' => $user->name,
            'company_name' => 'Example Company',
            'email' => 'new@example.com',
        ]);

        $response->assertSessionMissing('errors');
        $this->assertEquals('new@example.com', $user->fresh()->email);
        $this->assertNull($user->fresh()->email_verified_at);
    }
}
