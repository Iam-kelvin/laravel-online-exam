<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->questionBanks() as $subjectName => $questions) {
            $subject = Subject::firstOrCreate(
                ['name' => $subjectName],
                [
                    'slug' => $this->makeUniqueSlug($subjectName),
                    'active' => true,
                ]
            );

            foreach ($questions as $question) {
                Question::updateOrCreate(
                    ['question' => $question['question']],
                    array_merge($question, ['subject_id' => $subject->id])
                );
            }
        }
    }

    private function questionBanks(): array
    {
        return [
            'English Language' => [
                [
                    'question' => 'Choose the word nearest in meaning to "brief".',
                    'option_a' => 'Short',
                    'option_b' => 'Loud',
                    'option_c' => 'Heavy',
                    'option_d' => 'Late',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
                [
                    'question' => 'Which sentence is grammatically correct?',
                    'option_a' => 'She do her work daily.',
                    'option_b' => 'She does her work daily.',
                    'option_c' => 'She doing her work daily.',
                    'option_d' => 'She did her work daily everyday.',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Identify the adjective in this sentence: The bright lamp stayed on.',
                    'option_a' => 'Lamp',
                    'option_b' => 'Stayed',
                    'option_c' => 'Bright',
                    'option_d' => 'On',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
                [
                    'question' => 'Choose the correctly punctuated sentence.',
                    'option_a' => 'Where are you going.',
                    'option_b' => 'Where are you going?',
                    'option_c' => 'Where are you going,',
                    'option_d' => 'Where are you going!',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'The opposite of "generous" is ____.',
                    'option_a' => 'Kind',
                    'option_b' => 'Selfish',
                    'option_c' => 'Helpful',
                    'option_d' => 'Patient',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
            ],
            'Mathematics' => [
                [
                    'question' => 'What is the value of 8 x 7?',
                    'option_a' => '54',
                    'option_b' => '56',
                    'option_c' => '58',
                    'option_d' => '64',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Solve for x: x + 9 = 20.',
                    'option_a' => '9',
                    'option_b' => '10',
                    'option_c' => '11',
                    'option_d' => '12',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
                [
                    'question' => 'What is 25 percent of 80?',
                    'option_a' => '15',
                    'option_b' => '20',
                    'option_c' => '25',
                    'option_d' => '30',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'A triangle has angles 60 degrees and 50 degrees. What is the third angle?',
                    'option_a' => '60 degrees',
                    'option_b' => '65 degrees',
                    'option_c' => '70 degrees',
                    'option_d' => '80 degrees',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
                [
                    'question' => 'Which number is a prime number?',
                    'option_a' => '21',
                    'option_b' => '27',
                    'option_c' => '29',
                    'option_d' => '33',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
            ],
            'Biology' => [
                [
                    'question' => 'The basic unit of life is the ____.',
                    'option_a' => 'Tissue',
                    'option_b' => 'Cell',
                    'option_c' => 'Organ',
                    'option_d' => 'System',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Which organ pumps blood around the body?',
                    'option_a' => 'Liver',
                    'option_b' => 'Kidney',
                    'option_c' => 'Heart',
                    'option_d' => 'Lung',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
                [
                    'question' => 'Photosynthesis mainly occurs in the ____ of green plants.',
                    'option_a' => 'Roots',
                    'option_b' => 'Leaves',
                    'option_c' => 'Flowers',
                    'option_d' => 'Seeds',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Which blood cells help the body fight infection?',
                    'option_a' => 'White blood cells',
                    'option_b' => 'Red blood cells',
                    'option_c' => 'Platelets',
                    'option_d' => 'Plasma',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
                [
                    'question' => 'A habitat is best described as ____.',
                    'option_a' => 'A place where an organism lives',
                    'option_b' => 'A type of food chain',
                    'option_c' => 'A group of organs',
                    'option_d' => 'A method of reproduction',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
            ],
            'Chemistry' => [
                [
                    'question' => 'The chemical symbol for oxygen is ____.',
                    'option_a' => 'O',
                    'option_b' => 'Ox',
                    'option_c' => 'Og',
                    'option_d' => 'On',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
                [
                    'question' => 'Water is made up of hydrogen and ____.',
                    'option_a' => 'Nitrogen',
                    'option_b' => 'Oxygen',
                    'option_c' => 'Carbon',
                    'option_d' => 'Chlorine',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'A substance with pH less than 7 is usually ____.',
                    'option_a' => 'Acidic',
                    'option_b' => 'Neutral',
                    'option_c' => 'Basic',
                    'option_d' => 'Metallic',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
                [
                    'question' => 'Which state of matter has a fixed shape and fixed volume?',
                    'option_a' => 'Gas',
                    'option_b' => 'Liquid',
                    'option_c' => 'Solid',
                    'option_d' => 'Vapour',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
                [
                    'question' => 'The smallest particle of an element that retains its properties is an ____.',
                    'option_a' => 'Atom',
                    'option_b' => 'Mixture',
                    'option_c' => 'Compound',
                    'option_d' => 'Solution',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
            ],
            'Physics' => [
                [
                    'question' => 'The SI unit of force is the ____.',
                    'option_a' => 'Joule',
                    'option_b' => 'Newton',
                    'option_c' => 'Watt',
                    'option_d' => 'Volt',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Speed is calculated as distance divided by ____.',
                    'option_a' => 'Mass',
                    'option_b' => 'Time',
                    'option_c' => 'Force',
                    'option_d' => 'Energy',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Which device is used to measure electric current?',
                    'option_a' => 'Voltmeter',
                    'option_b' => 'Ammeter',
                    'option_c' => 'Thermometer',
                    'option_d' => 'Barometer',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Light travels fastest in ____.',
                    'option_a' => 'Water',
                    'option_b' => 'Glass',
                    'option_c' => 'Air',
                    'option_d' => 'Vacuum',
                    'answer' => 'option_d',
                    'duration' => 60,
                ],
                [
                    'question' => 'Energy possessed by a body because of its motion is ____ energy.',
                    'option_a' => 'Potential',
                    'option_b' => 'Chemical',
                    'option_c' => 'Kinetic',
                    'option_d' => 'Nuclear',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
            ],
            'Government' => [
                [
                    'question' => 'Democracy is a system of government by the ____.',
                    'option_a' => 'Military',
                    'option_b' => 'People',
                    'option_c' => 'Judges only',
                    'option_d' => 'Civil service only',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'The arm of government that interprets laws is the ____.',
                    'option_a' => 'Executive',
                    'option_b' => 'Legislature',
                    'option_c' => 'Judiciary',
                    'option_d' => 'Electorate',
                    'answer' => 'option_c',
                    'duration' => 60,
                ],
                [
                    'question' => 'A constitution is best described as ____.',
                    'option_a' => 'A national anthem',
                    'option_b' => 'A body of fundamental laws',
                    'option_c' => 'A political party',
                    'option_d' => 'A voting card',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Universal adult suffrage means ____.',
                    'option_a' => 'Voting by adults who meet the law',
                    'option_b' => 'Voting by children',
                    'option_c' => 'Voting by rulers only',
                    'option_d' => 'Voting by soldiers only',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
                [
                    'question' => 'The legislature is mainly responsible for ____.',
                    'option_a' => 'Making laws',
                    'option_b' => 'Arresting offenders',
                    'option_c' => 'Trying court cases',
                    'option_d' => 'Printing money',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
            ],
            'Economics' => [
                [
                    'question' => 'Economics studies how people allocate ____ resources.',
                    'option_a' => 'Unlimited',
                    'option_b' => 'Scarce',
                    'option_c' => 'Useless',
                    'option_d' => 'Invisible',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Demand means the quantity consumers are willing and able to ____.',
                    'option_a' => 'Produce',
                    'option_b' => 'Buy',
                    'option_c' => 'Destroy',
                    'option_d' => 'Tax',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'Land, labour, capital, and entrepreneur are called factors of ____.',
                    'option_a' => 'Consumption',
                    'option_b' => 'Production',
                    'option_c' => 'Inflation',
                    'option_d' => 'Taxation',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
                [
                    'question' => 'A rise in the general price level is called ____.',
                    'option_a' => 'Inflation',
                    'option_b' => 'Deflation',
                    'option_c' => 'Barter',
                    'option_d' => 'Saving',
                    'answer' => 'option_a',
                    'duration' => 60,
                ],
                [
                    'question' => 'Opportunity cost is the ____ alternative forgone.',
                    'option_a' => 'Least valuable',
                    'option_b' => 'Next best',
                    'option_c' => 'Unknown',
                    'option_d' => 'Cheapest',
                    'answer' => 'option_b',
                    'duration' => 60,
                ],
            ],
        ];
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
