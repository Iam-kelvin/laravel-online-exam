<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Subject;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BulkImport implements ToModel, SkipsEmptyRows, WithHeadingRow
{
    public function model(array $row)
    {
        $subjectName = $this->value($row, ['subject', 'subject_name']);
        $questionText = $this->value($row, ['question']);
        $optionA = $this->value($row, ['option_a', 'a']);
        $optionB = $this->value($row, ['option_b', 'b']);
        $optionC = $this->value($row, ['option_c', 'c']);
        $optionD = $this->value($row, ['option_d', 'd']);
        $answer = $this->normalizeAnswer($this->value($row, ['answer', 'correct_answer']));

        if (
            $subjectName === ''
            || $questionText === ''
            || $optionA === ''
            || $optionB === ''
            || $optionC === ''
            || $optionD === ''
            || $answer === ''
        ) {
            throw ValidationException::withMessages([
                'file' => 'Each imported row must include subject, question, option_a, option_b, option_c, option_d, and answer.',
            ]);
        }

        $subject = $this->subjectFor($subjectName);

        Question::updateOrCreate(
            [
                'subject_id' => $subject->id,
                'question' => $questionText,
            ],
            [
                'option_a' => $optionA,
                'option_b' => $optionB,
                'option_c' => $optionC,
                'option_d' => $optionD,
                'answer' => $answer,
            ]
        );

        return null;
    }

    private function value(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null) {
                return trim((string) $row[$key]);
            }
        }

        return '';
    }

    private function normalizeAnswer(string $answer): string
    {
        $normalized = Str::lower(trim($answer));
        $normalized = str_replace([' ', '-', '.'], '_', $normalized);

        return match ($normalized) {
            'a', 'option_a' => 'option_a',
            'b', 'option_b' => 'option_b',
            'c', 'option_c' => 'option_c',
            'd', 'option_d' => 'option_d',
            default => '',
        };
    }

    private function subjectFor(string $name): Subject
    {
        $cleanName = trim(preg_replace('/\s+/', ' ', $name));
        $existing = Subject::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($cleanName)])
            ->first();

        if ($existing) {
            return $existing;
        }

        return Subject::create([
            'name' => $cleanName,
            'slug' => $this->makeUniqueSlug($cleanName),
            'active' => true,
        ]);
    }

    private function makeUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'subject';
        $slug = $baseSlug;
        $suffix = 2;

        while (Subject::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
