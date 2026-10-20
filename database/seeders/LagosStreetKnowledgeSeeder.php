<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Subject;
use App\Services\LagosQuestionBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LagosStreetKnowledgeSeeder extends Seeder
{
    private const LEGACY_TERMS = [
        'Eyo festival', 'Third Mainland Bridge', 'Balogun Market', 'National Theatre',
        'Tafawa Balewa Square', 'Makoko', 'Lekki Conservation Centre', 'BRT', 'Marina',
        'Badagry', 'Tinubu Square', 'Freedom Park', 'CMS', 'Idumota', 'Oshodi',
        'Mile 12 Market', 'Computer Village', 'Alaba International Market', 'Bar Beach',
        'Elegushi Beach', 'Ikoyi', 'Yaba', 'Surulere', 'Apapa Port', 'Carter Bridge',
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $subject = Subject::whereRaw('LOWER(name) = ?', ['lagos street knowledge'])->first();
            if (! $subject) {
                $subject = Subject::firstOrCreate(['slug' => 'lagos-street-knowledge'], [
                    'name' => 'Lagos Street Knowledge', 'active' => true, 'bank_type' => Subject::TYPE_CHALLENGE,
                ]);
            }
            $subject->update(['description' => 'Markets, bus stops, routes and old Lagos. How well do you know your city?']);

            // Retire only the old generated templates in this bank; keep custom questions.
            $subject->questions()->where(function ($query) {
                foreach ([
                    'In Lagos Street Knowledge, %', 'A student reviewing Lagos Street Knowledge %',
                    'Which Lagos Street Knowledge idea %', 'Lagos Street Knowledge bank drill %',
                ] as $pattern) {
                    $query->orWhere('question', 'like', $pattern);
                }
                $query->orWhereIn('question', array_map(
                    fn ($term) => 'Which statement best describes ' . $term . '?', self::LEGACY_TERMS
                ));
            })->delete();

            foreach ((new LagosQuestionBank)->rows() as $row) {
                Question::updateOrCreate(['subject_id' => $subject->id, 'question' => $row['question']], $row);
            }
        });
    }
}
