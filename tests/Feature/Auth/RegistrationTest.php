<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/en/register')->assertStatus(200);
    }

    public function test_first_user_registers_as_admin_and_is_redirected_to_verify(): void
    {
        $response = $this->post('/en/register', [
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/en/verify-email');
        $this->assertDatabaseHas('users', ['email' => 'owner@example.com', 'role' => 'admin']);
    }

    public function test_subsequent_user_registers_as_default_user_role(): void
    {
        User::factory()->create(); // First user exists

        $this->post('/en/register', [
            'name' => 'Warehouse Staff',
            'email' => 'staff@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'staff@example.com', 'role' => 'user']);
    }

    public function test_user_can_verify_email_with_6_digit_code(): void
    {
        $user = User::factory()->unverified()->create([
            'verification_code' => '123456',
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->actingAs($user)->post('/en/verify-code', [
            'code' => '123456',
        ]);

        $response->assertRedirect('/en/dashboard');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertNull($user->fresh()->verification_code);
    }

    public function test_only_pin_mail_is_sent_on_registration(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Notification::fake();

        $this->post('/en/register', [
            'name' => 'Pin User',
            'email' => 'pin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\VerificationCodeMail::class, 1);
        \Illuminate\Support\Facades\Notification::assertNothingSent();
    }
}
