<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'What is the capital of France?',
                'option_a' => 'London',
                'option_b' => 'Paris',
                'option_c' => 'Berlin',
                'option_d' => 'Madrid',
                'answer' => 'option_b',
                'duration' => 60,
            ],
            [
                'question' => 'What is 2 + 2?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'answer' => 'option_b',
                'duration' => 60,
            ],
            [
                'question' => 'What is 5 + 2?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '7',
                'answer' => 'option_d',
                'duration' => 60,
            ],
            [
                'question' => 'What is 1 + 2?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'answer' => 'option_a',
                'duration' => 60,
            ],
            [
                'question' => 'What is 3 + 2?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'answer' => 'option_c',
                'duration' => 60,
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
