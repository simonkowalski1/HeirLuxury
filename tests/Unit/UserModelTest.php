<?php

// ABOUTME: Unit tests for the User model.
// ABOUTME: Covers isAdmin(), mass assignment protection, and attribute casting.

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_admin_returns_true_for_admin_user(): void
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_is_admin_returns_false_for_regular_user(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
    }

    public function test_is_admin_is_not_mass_assignable(): void
    {
        $user = User::factory()->create();

        // Attempt to mass-assign is_admin via fill()
        $user->fill(['is_admin' => true]);
        $user->save();

        // is_admin should still be false because it's not in $fillable
        $this->assertFalse($user->fresh()->isAdmin());
    }

    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create(['password' => 'plain-text-password']);

        // Password should be hashed, not stored in plain text
        $this->assertNotEquals('plain-text-password', $user->password);
        $this->assertTrue(password_verify('plain-text-password', $user->password));
    }

    public function test_is_admin_is_cast_to_boolean(): void
    {
        $user = User::factory()->create();
        $user->is_admin = 1;
        $user->save();

        $fresh = $user->fresh();

        $this->assertIsBool($fresh->is_admin);
        $this->assertTrue($fresh->is_admin);
    }

    public function test_user_has_correct_fillable_attributes(): void
    {
        $user = new User;

        $expected = ['name', 'email', 'password'];

        $this->assertEquals($expected, $user->getFillable());
    }

    public function test_user_has_correct_hidden_attributes(): void
    {
        $user = new User;

        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }
}
