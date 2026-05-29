<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_open_dashboard_and_start_exam_without_email_verification(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('exam.start'))
            ->assertOk();

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

    public function test_email_change_requires_current_password(): void
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
