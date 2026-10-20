<?php

namespace App\Services;

class PuzzleBank
{
    public const GAMES = [
        'word-scramble' => [
            'name' => 'Word Scramble', 'icon' => 'letters',
            'description' => 'Mixed-up letters. One word. How many can you untangle before time runs out?',
            'instruction' => 'Use every letter to make a word. The clue points you in the right direction.',
        ],
        'picture-puzzles' => [
            'name' => 'Picture Puzzles', 'icon' => 'picture',
            'description' => 'Look closer. The words, shapes and positions hide a familiar phrase. Can you spot it?',
            'instruction' => 'Say what you see. Type the phrase hidden in the picture.',
        ],
    ];

    public function questions(string $game): array
    {
        if ($game === 'word-scramble') {
            return array_map(function (array $word) {
                [$answer, $hint] = $word;
                do { $letters = str_shuffle(strtoupper($answer)); } while ($letters === strtoupper($answer));

                return $this->question('Unscramble this word.', $answer, 'scramble', [
                    'letters' => $letters, 'hint' => $hint,
                ]);
            }, $this->words());
        }

        $puzzles = $this->rebuses();
        return array_map(fn (array $puzzle, int $index) => $this->question(
            'What does this picture say?', $puzzle[1], 'rebus',
            ['image' => sprintf('puzzles/rebus/puzzle-%02d.svg', $index + 1), 'alt' => $puzzle[2]],
            $puzzle[3] ?? []
        ), $puzzles, array_keys($puzzles));
    }

    private function question(string $prompt, string $answer, string $type, array $data, array $aliases = []): array
    {
        return [
            'question_text' => $prompt, 'question_type' => $type,
            'prompt_data' => $data, 'correct_answer' => $answer, 'answer_aliases' => $aliases,
            'option_a' => '', 'option_b' => '', 'option_c' => '', 'option_d' => '',
        ];
    }

    private function words(): array
    {
        return [
            ['market', 'A place to buy and sell.'], ['bridge', 'It takes you across a river.'],
            ['lagoon', 'A shallow coastal body of water.'], ['street', 'A road lined with buildings.'],
            ['island', 'Land surrounded by water.'], ['traffic', 'Too many vehicles on the road.'],
            ['ticket', 'Your pass for a ride or show.'], ['yellow', 'The colour of a Lagos danfo.'],
            ['driver', 'The person behind the wheel.'], ['pepper', 'It gives your food a spicy kick.'],
            ['museum', 'A home for historical exhibits.'], ['school', 'A place where pupils learn.'],
            ['beach', 'Sand beside the sea.'], ['palace', 'A royal residence.'],
            ['bottle', 'A narrow-necked drink container.'], ['window', 'Glass you look through in a wall.'],
            ['garden', 'A place to grow flowers.'], ['camera', 'It captures photographs.'],
            ['basket', 'A woven container with a handle.'], ['silver', 'A precious metal used in jewellery.'],
            ['orange', 'A citrus fruit with a thick peel.'], ['planet', 'A large body orbiting a star.'],
            ['pencil', 'You can erase what it writes.'], ['pillow', 'Rest your head on it.'],
            ['rabbit', 'An animal with long ears.'], ['shadow', 'A dark shape cast by blocked light.'],
            ['wallet', 'A small holder for money and cards.'], ['winter', 'The coldest season.'],
            ['forest', 'A large area covered with trees.'], ['rocket', 'It launches into space.'],
            ['turtle', 'A reptile with a protective shell.'], ['anchor', 'It holds a ship in place.'],
            ['castle', 'A fortified medieval home.'], ['candle', 'A wax light with a wick.'],
            ['puzzle', 'A problem you solve for fun.'], ['rhythm', 'A pattern of musical beats.'],
            ['library', 'A place to borrow books.'], ['thunder', 'The sound after lightning.'],
            ['journey', 'A trip from one place to another.'], ['blanket', 'A warm bed covering.'],
            ['diamond', 'A very hard precious gemstone.'], ['feather', 'It covers a bird.'],
            ['kitchen', 'The room where you cook.'], ['lantern', 'A portable light in a case.'],
            ['rainbow', 'A colourful arc after rain.'], ['teacher', 'The person leading a lesson.'],
            ['balance', 'Keeping your weight steady.'], ['whistle', 'A small device that makes a shrill sound.'],
            ['village', 'A settlement smaller than a town.'], ['bicycle', 'A two-wheeled pedal vehicle.'],
            ['chocolate', 'A sweet treat made with cocoa.'], ['elephant', 'An animal with a trunk.'],
            ['umbrella', 'Hold it over your head in the rain.'], ['hospital', 'A place for medical treatment.'],
            ['football', 'A game played with goals and a ball.'], ['calendar', 'It shows the days and months.'],
            ['mountain', 'A very high natural rise in the land.'], ['sandwich', 'A filling between slices of bread.'],
            ['treasure', 'A valuable hidden collection.'], ['keyboard', 'Type on its keys.'],
            ['sunshine', 'Light from the sun.'], ['passport', 'Your identity document for foreign travel.'],
            ['festival', 'A celebration with events and performances.'], ['adventure', 'An exciting, unusual experience.'],
            ['breakfast', 'The first meal of the day.'], ['butterfly', 'An insect with broad, colourful wings.'],
            ['crocodile', 'A large reptile with powerful jaws.'], ['pineapple', 'A tropical fruit with a spiky crown.'],
            ['telephone', 'A device for making calls.'], ['waterfall', 'Water dropping over a cliff.'],
            ['bookshop', 'A store that sells books.'], ['fireworks', 'Colourful explosions in the night sky.'],
        ];
    }

    private function rebuses(): array
    {
        return [
            ['four-ones', 'For once in my life', 'MY LIFE with four numerals 1 inserted among the letters.'],
            ['head-heels', 'Head over heels', 'HEAD above HEELS.'],
            ['reading-lines', 'Reading between the lines', 'READING placed between two horizontal lines.', ['Read between the lines']],
            ['man-board', 'Man overboard', 'MAN above BOARD.'],
            ['stand-under', 'I understand', 'STAND above the letter I.'],
            ['mind-matter', 'Mind over matter', 'MIND above MATTER.'],
            ['cycle-three', 'Tricycle', 'CYCLE written three times.'],
            ['life-life', 'Double life', 'LIFE written twice.'],
            ['time-time', 'Time after time', 'TIME followed by TIME.'],
            ['just-time', 'Just in time', 'JUST placed inside the word TIME.'],
            ['broken-heart', 'Broken heart', 'HE and ART separated by a jagged crack.'],
            ['split-second', 'Split second', 'SEC and OND separated by a vertical gap.'],
            ['miss-you', 'Missing you', 'The alphabet with U absent.', ['Missing U']],
            ['backward-glance', 'Backward glance', 'The letters ECNALG.', ['Backwards glance']],
            ['touch', 'Out of touch', 'TOUCH outside a square.'],
            ['box-thinking', 'Thinking outside the box', 'THINKING outside a square.', ['Think outside the box']],
            ['secret', 'Top secret', 'SECRET at the top of a large square.'],
            ['bottom-line', 'Bottom line', 'LINE at the bottom of a large square.', ['The bottom line']],
            ['ground', 'Underground', 'GROUND below a horizontal line.'],
            ['water-bridge', 'Water under the bridge', 'BRIDGE above a line, WATER beneath it.', ['Water under a bridge']],
            ['level', 'Split level', 'LE and VEL on different levels.'],
            ['feet-six', 'Six feet under', 'Six copies of FEET beneath a line.'],
            ['noon', 'High noon', 'NOON high above a baseline.'],
            ['world', 'Four corners of the world', 'WORLD written in each of four corners.', ['The four corners of the world']],
        ];
    }
}
