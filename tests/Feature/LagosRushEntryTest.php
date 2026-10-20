<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LagosRushEntryTest extends TestCase
{
    use DatabaseTransactions;

    private function lagos(): Subject
    {
        $subject = Subject::firstOrCreate(['name' => 'Lagos Street Knowledge'], [
            'slug' => 'lagos-street-knowledge', 'bank_type' => 'challenge', 'active' => true,
        ]);
        $subject->update(['active' => true]);
        $subject->questions()->create([
            'question' => 'Where is Computer Village?', 'option_a' => 'Ikeja', 'option_b' => 'Epe',
            'option_c' => 'Ikoyi', 'option_d' => 'Yaba', 'answer' => 'option_a',
        ]);

        return $subject;
    }

    public function test_lagos_shortcut_selects_the_bank_and_default_timer_without_starting_an_exam(): void
    {
        $subject = $this->lagos();
        $subject->update(['slug' => 'lagos-entry-test']);
        $user = User::factory()->create();
        $setup = route('exam.start', ['mode' => 'rush', 'subject_ids' => [$subject->id], 'duration_seconds' => 60]);
        $this->actingAs($user)->get(route('exam.lagos-rush'))->assertRedirect($setup);
        $this->get($setup)->assertOk()->assertSee('Lagos Rush')
            ->assertViewHas('selectedMode', 'rush')->assertViewHas('selectedDuration', 60)
            ->assertViewHas('selectedSubjectIds', [$subject->id]);
        $this->assertSame(0, $user->examAttempts()->count());

        $this->post(route('exam.store'), ['mode' => 'rush', 'subject_ids' => [$subject->id], 'duration_seconds' => 75])->assertSessionHasNoErrors();
        $attempt = $user->examAttempts()->firstOrFail();
        $this->assertTrue($attempt->isLagosRush());
        $this->assertSame(75, $attempt->duration_seconds);
        $this->get(route('exam.take', $attempt))->assertOk()->assertSee('Lagos Rush');
    }

    public function test_guest_returns_to_the_lagos_shortcut_after_login(): void
    {
        $this->lagos();
        $user = User::factory()->create(['password' => Hash::make('LagosTest123!')]);
        $this->get(route('exam.lagos-rush'))->assertRedirect(route('login'))->assertSessionHas('url.intended', route('exam.lagos-rush'));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'LagosTest123!'])->assertRedirect(route('exam.lagos-rush'));
    }

    public function test_unavailable_lagos_bank_falls_back_without_creating_an_attempt(): void
    {
        $subject = $this->lagos();
        $subject->update(['active' => false]);
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('exam.lagos-rush'))
            ->assertRedirect(route('exam.start', ['mode' => 'rush']))->assertSessionHas('warning');
        $this->assertSame(0, $user->examAttempts()->count());
    }
}
