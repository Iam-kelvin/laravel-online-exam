<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BulkImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Question([
            'question'     => $row['question'],
            'option_a'    => $row['option_a'],
            'option_b'    => $row['option_b'],
            'option_c'    => $row['option_c'],
            'option_d'    => $row['option_d'],
            'answer'    => $row['answer'],
            'duration'    => $row['duration'],
        ]);
    }
}
