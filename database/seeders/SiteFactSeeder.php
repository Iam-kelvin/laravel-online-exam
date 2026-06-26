<?php

namespace Database\Seeders;

use App\Models\SiteFact;
use Illuminate\Database\Seeder;

class SiteFactSeeder extends Seeder
{
    public function run(): void
    {
        $facts = [
            ['fact', 'Tiny Edge', 'A focused 20-minute practice can reveal weak spots faster than rereading notes for an hour.'],
            ['quote', 'Exam Mindset', 'Speed is useful, but accuracy is what turns effort into a score worth sharing.'],
            ['fact', 'Did You Know?', 'Lagos was the capital of Nigeria before Abuja officially became the capital in 1991.'],
            ['fact', 'Memory Trick', 'Explaining an answer out loud helps your brain test whether you truly understand it.'],
            ['quote', 'Practice Note', 'One clean attempt today beats ten imaginary perfect attempts tomorrow.'],
            ['fact', 'Science Bite', 'The mitochondrion is often called the powerhouse of the cell because it helps release usable energy.'],
            ['fact', 'Language Bite', 'Reading comprehension improves when you predict the main idea before checking the questions.'],
            ['quote', 'Challenge Mode', 'If your score makes you smile, share the card and let your friends chase it.'],
        ];

        foreach ($facts as [$kind, $title, $body]) {
            SiteFact::updateOrCreate(
                ['title' => $title, 'body' => $body],
                ['kind' => $kind, 'active' => true]
            );
        }
    }
}
