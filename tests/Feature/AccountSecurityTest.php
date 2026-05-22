<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unverified_user_can_open_dashboard_but_not_start_exam(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Confirm your email address');

        $this->actingAs($user)
            ->get(route('exam.start'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($user)
            ->get(route('exam.results'))
            ->assertOk();
    }

    public function test_user_can_change_password_from_account_settings(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($user)
            ->patch(route('account.password.update'), [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('account.edit'));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_email_change_requires_current_password_and_sends_verification(): void
    {
        $user = User::factory()->create([
            'email' => 'learner@example.test',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->patch(route('account.profile.update'), [
                'name' => 'Learner Updated',
                'email' => 'new-learner@example.test',
            ])
            ->assertSessionHasErrors(['current_password'], null, 'profile');

        Notification::fake();

        $this->actingAs($user)
            ->patch(route('account.profile.update'), [
                'name' => 'Learner Updated',
                'email' => 'new-learner@example.test',
                'current_password' => 'password',
            ])
            ->assertRedirect(route('account.edit'));

        $user->refresh();

        $this->assertSame('Learner Updated', $user->name);
        $this->assertSame('new-learner@example.test', $user->email);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_forgot_password_sends_reset_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset-me@example.test',
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
