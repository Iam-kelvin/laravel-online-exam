<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class BulkImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $subjectName = $row['subject'] ?? $row['subject_name'] ?? 'General';
        $subject = Subject::firstOrCreate(
            ['name' => $subjectName],
            [
                'slug' => $this->makeUniqueSlug($subjectName),
                'active' => true,
            ]
        );

        return new Question([
            'subject_id'    => $subject->id,
            'question'     => $row['question'],
            'option_a'    => $row['option_a'],
            'option_b'    => $row['option_b'],
            'option_c'    => $row['option_c'],
            'option_d'    => $row['option_d'],
            'answer'    => $row['answer'],
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
