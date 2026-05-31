<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    private const DURATION_SECONDS = 60;

    public function run(): void
    {
        $this->moveGeographyQuestions();
        $this->removeRetiredGeneralQuestions();
        $this->removeOldSampleQuestions();

        foreach ($this->questionBanks() as $subjectName => $questions) {
            $subject = $this->subjectFor($subjectName);

            foreach ($questions as $question) {
                Question::updateOrCreate(
                    ['question' => $question['question']],
                    array_merge($question, ['subject_id' => $subject->id])
                );
            }
        }

        $this->removeDuplicateQuestion('What is the capital of France?');
        $this->deactivateEmptyGeneralSubject();
    }

    private function questionBanks(): array
    {
        return [
            'Biology' => $this->conceptQuestions('Biology', [
                ['Cell', 'the basic structural and functional unit of life'],
                ['Nucleus', 'the part of a cell that controls most cell activities'],
                ['Mitochondrion', 'the cell organelle where most energy is released'],
                ['Chlorophyll', 'the green pigment that absorbs light for photosynthesis'],
                ['Photosynthesis', 'the process by which green plants make food using light'],
                ['Respiration', 'the process by which cells release energy from food'],
                ['Tissue', 'a group of similar cells working together'],
                ['Organ', 'a body part made of tissues that performs a function'],
                ['Enzyme', 'a biological catalyst that speeds up reactions'],
                ['Diffusion', 'movement of particles from high concentration to low concentration'],
                ['Osmosis', 'movement of water molecules through a selectively permeable membrane'],
                ['Habitat', 'the natural place where an organism lives'],
                ['Ecosystem', 'a community of organisms interacting with their environment'],
                ['Producer', 'an organism that makes its own food'],
                ['Consumer', 'an organism that feeds on other organisms'],
                ['Decomposer', 'an organism that breaks down dead organic matter'],
                ['Herbivore', 'an animal that feeds mainly on plants'],
                ['Carnivore', 'an animal that feeds mainly on other animals'],
                ['Omnivore', 'an animal that feeds on both plants and animals'],
                ['Genetics', 'the study of heredity and variation'],
                ['Chromosome', 'a thread-like structure that carries genes'],
                ['DNA', 'the molecule that carries hereditary information'],
                ['Vaccine', 'a preparation that stimulates immunity against disease'],
                ['Pathogen', 'a microorganism that can cause disease'],
                ['Homeostasis', 'maintenance of a stable internal body environment'],
                ['Microscope', 'an instrument used to view very small objects'],
                ['Hygiene', 'practices that help maintain health and cleanliness'],
                ['First aid', 'immediate help given to an injured or sick person'],
                ['Recycling', 'processing used materials so they can be used again'],
            ]),
            'Chemistry' => $this->conceptQuestions('Chemistry', [
                ['Atom', 'the smallest particle of an element that keeps its properties'],
                ['Element', 'a pure substance made of only one type of atom'],
                ['Compound', 'a substance formed when elements chemically combine'],
                ['Molecule', 'two or more atoms chemically joined together'],
                ['Mixture', 'substances physically combined without chemical bonding'],
                ['Solution', 'a uniform mixture of solute and solvent'],
                ['Solvent', 'the substance that dissolves a solute'],
                ['Solute', 'the substance dissolved in a solvent'],
                ['Acid', 'a substance with pH less than 7'],
                ['Base', 'a substance with pH greater than 7'],
                ['Neutralization', 'a reaction between an acid and a base'],
                ['Catalyst', 'a substance that changes reaction speed without being used up'],
                ['Ion', 'an atom or group of atoms with an electric charge'],
                ['Covalent bond', 'a bond formed by sharing electrons'],
                ['Ionic bond', 'a bond formed by transfer of electrons'],
                ['pH scale', 'a scale used to measure acidity or alkalinity'],
                ['Oxidation', 'loss of electrons during a chemical reaction'],
                ['Reduction', 'gain of electrons during a chemical reaction'],
                ['Electrolysis', 'chemical decomposition caused by electric current'],
                ['Hydrocarbon', 'a compound containing only carbon and hydrogen'],
                ['Polymer', 'a large molecule made from repeating smaller units'],
                ['Isotope', 'atoms of the same element with different mass numbers'],
                ['Mole', 'the amount of substance containing Avogadro number of particles'],
                ['Exothermic reaction', 'a reaction that releases heat energy'],
                ['Endothermic reaction', 'a reaction that absorbs heat energy'],
            ]),
            'Computer Studies' => $this->conceptQuestions('Computer Studies', [
                ['Computer', 'an electronic device that accepts data and processes it'],
                ['Hardware', 'the physical parts of a computer system'],
                ['Software', 'programs and instructions used by a computer'],
                ['Keyboard', 'an input device with keys for typing'],
                ['Mouse', 'an input device used to point and select items'],
                ['Monitor', 'an output device that displays information visually'],
                ['Printer', 'an output device that produces hard copies'],
                ['Scanner', 'an input device that captures images or documents'],
                ['Calculator', 'a device used to perform mathematical operations'],
                ['CPU', 'the main processing unit of a computer'],
                ['Memory', 'the part of a computer used to store data temporarily'],
                ['Storage', 'the part of a computer used to keep data permanently'],
                ['File', 'a named collection of stored data'],
                ['Folder', 'a container used to organize files'],
                ['Internet', 'a worldwide network of connected computers'],
                ['Browser', 'software used to access web pages'],
                ['Email', 'a system for sending messages electronically'],
                ['Password', 'a secret code used to protect access'],
                ['Database', 'an organized collection of data'],
                ['Spreadsheet', 'software used to arrange data in rows and columns'],
                ['Word processor', 'software used to create and edit text documents'],
                ['Algorithm', 'a step-by-step method for solving a problem'],
                ['Debugging', 'finding and correcting errors in a program'],
                ['Cybersecurity', 'protecting computer systems and data from attack'],
                ['Backup', 'a copy of data kept for recovery'],
            ]),
            'Economics' => $this->conceptQuestions('Economics', [
                ['Scarcity', 'limited resources compared with unlimited wants'],
                ['Choice', 'selecting one option from available alternatives'],
                ['Opportunity cost', 'the next best alternative forgone'],
                ['Demand', 'quantity consumers are willing and able to buy'],
                ['Supply', 'quantity producers are willing and able to sell'],
                ['Market', 'any arrangement where buyers and sellers exchange goods'],
                ['Equilibrium price', 'the price where quantity demanded equals quantity supplied'],
                ['Inflation', 'a sustained rise in the general price level'],
                ['Deflation', 'a sustained fall in the general price level'],
                ['Utility', 'satisfaction gained from consuming a good or service'],
                ['Production', 'the process of creating goods and services'],
                ['Land', 'natural resources used in production'],
                ['Labour', 'human effort used in production'],
                ['Capital', 'man-made resources used for further production'],
                ['Entrepreneur', 'the organizer who combines factors of production'],
                ['Consumer', 'a person who buys goods or services for use'],
                ['Producer', 'a person or firm that makes goods or services'],
                ['Profit', 'the excess of revenue over cost'],
                ['Revenue', 'income received from selling goods or services'],
                ['Cost', 'expenses incurred in production'],
                ['Tax', 'a compulsory payment to government'],
                ['Subsidy', 'financial support given to reduce production cost'],
                ['Gross domestic product', 'total value of final goods and services produced in a country'],
                ['Exchange rate', 'the price of one currency in terms of another'],
                ['Balance of trade', 'the difference between visible exports and visible imports'],
            ]),
            'English Language' => $this->englishQuestions(),
            'Geography' => $this->geographyQuestions(),
            'Government' => $this->conceptQuestions('Government', [
                ['Democracy', 'government in which people choose their leaders'],
                ['Constitution', 'a body of fundamental laws of a state'],
                ['Legislature', 'the arm of government that makes laws'],
                ['Executive', 'the arm of government that implements laws'],
                ['Judiciary', 'the arm of government that interprets laws'],
                ['Rule of law', 'the principle that everyone is subject to the law'],
                ['Separation of powers', 'division of government powers among different arms'],
                ['Federalism', 'sharing powers between central and regional governments'],
                ['Suffrage', 'the legal right to vote in elections'],
                ['Election', 'a process of choosing leaders by voting'],
                ['Political party', 'an organization seeking political power through elections'],
                ['Civil service', 'permanent officials who implement government policies'],
                ['Citizen', 'a legal member of a state'],
                ['Sovereignty', 'supreme power of a state over its affairs'],
                ['Monarchy', 'government headed by a king or queen'],
                ['Republic', 'a state with an elected or non-hereditary head'],
                ['Referendum', 'a direct vote by citizens on a public issue'],
                ['Cabinet', 'senior ministers who advise the head of government'],
                ['Bill', 'a proposed law before it is passed'],
                ['Law', 'a rule made and enforced by government'],
                ['Human rights', 'basic freedoms and protections people should enjoy'],
                ['Pressure group', 'an organization that influences government decisions'],
                ['Public opinion', 'the views held by members of the public'],
                ['Local government', 'government that manages affairs at community level'],
                ['Checks and balances', 'controls that prevent abuse of power'],
                ['Road signs', 'symbols that guide and warn road users'],
                ['Traffic light', 'a signal device that controls road movement'],
                ['Citizenship', 'membership of a country with rights and duties'],
                ['Culture', 'the way of life of a group of people'],
            ]),
            'Mathematics' => $this->mathematicsQuestions(),
            'Physics' => $this->conceptQuestions('Physics', [
                ['Force', 'a push or pull that can change motion'],
                ['Newton', 'the SI unit of force'],
                ['Velocity', 'speed in a specified direction'],
                ['Acceleration', 'rate of change of velocity'],
                ['Momentum', 'the product of mass and velocity'],
                ['Work', 'force multiplied by distance moved in the force direction'],
                ['Power', 'rate at which work is done'],
                ['Energy', 'capacity to do work'],
                ['Kinetic energy', 'energy possessed by a moving body'],
                ['Potential energy', 'stored energy due to position or condition'],
                ['Pressure', 'force acting per unit area'],
                ['Density', 'mass per unit volume'],
                ['Current', 'rate of flow of electric charge'],
                ['Voltage', 'potential difference between two points'],
                ['Resistance', 'opposition to the flow of electric current'],
                ['Ohms law', 'the relation V equals IR in an electric circuit'],
                ['Frequency', 'number of complete waves per second'],
                ['Wavelength', 'distance between two corresponding points on a wave'],
                ['Reflection', 'bouncing back of light from a surface'],
                ['Refraction', 'bending of light as it passes between media'],
                ['Heat', 'energy transferred because of temperature difference'],
                ['Temperature', 'degree of hotness or coldness of a body'],
                ['Magnetism', 'force caused by magnets or moving electric charges'],
                ['Gravity', 'attractive force between masses'],
                ['Transformer', 'a device that changes alternating voltage'],
                ['Thermometer', 'an instrument used to measure temperature'],
                ['Telescope', 'an instrument used to view distant objects'],
                ['Stopwatch', 'a device used to measure short intervals of time'],
                ['Renewable energy', 'energy from sources that can be naturally replaced'],
                ['Fossil fuel', 'fuel formed from ancient organic matter'],
            ]),
        ];
    }

    private function englishQuestions(): array
    {
        $words = [
            ['brief', 'short', 'lengthy'],
            ['generous', 'kind', 'selfish'],
            ['ancient', 'old', 'modern'],
            ['brave', 'courageous', 'cowardly'],
            ['scarce', 'rare', 'abundant'],
            ['rapid', 'fast', 'slow'],
            ['silent', 'quiet', 'noisy'],
            ['expand', 'increase', 'reduce'],
            ['honest', 'truthful', 'dishonest'],
            ['fragile', 'delicate', 'strong'],
            ['accurate', 'correct', 'incorrect'],
            ['vacant', 'empty', 'occupied'],
            ['purchase', 'buy', 'sell'],
            ['assist', 'help', 'hinder'],
            ['reject', 'refuse', 'accept'],
            ['gloomy', 'dull', 'cheerful'],
            ['difficult', 'hard', 'easy'],
            ['commence', 'begin', 'end'],
            ['tiny', 'small', 'huge'],
            ['wealthy', 'rich', 'poor'],
            ['calm', 'peaceful', 'agitated'],
            ['frequent', 'often', 'rare'],
            ['permit', 'allow', 'forbid'],
            ['repair', 'fix', 'damage'],
            ['observe', 'watch', 'ignore'],
        ];

        $questions = [];
        foreach ($words as $index => [$word, $meaning, $opposite]) {
            $questions[] = $this->makeQuestion(
                'Choose the word nearest in meaning to "' . $word . '".',
                $meaning,
                $this->neighborValues($words, $index, 1),
                $index
            );

            $questions[] = $this->makeQuestion(
                'Choose the word opposite in meaning to "' . $word . '".',
                $opposite,
                $this->neighborValues($words, $index, 2),
                $index + 1
            );
        }

        return array_merge(
            $questions,
            $this->comprehensionQuestions(),
            $this->conceptQuestions('English Language', [
                ['Dictionary', 'a resource that gives word meanings'],
                ['Library', 'a place where books and information resources are kept'],
                ['Communication', 'the exchange of information between people'],
                ['Sentence', 'a group of words that expresses a complete thought'],
                ['Paragraph', 'a group of sentences about one main idea'],
                ['Grammar', 'the rules that guide correct language use'],
            ])
        );
    }

    private function geographyQuestions(): array
    {
        $capitals = [
            ['France', 'Paris'],
            ['Egypt', 'Cairo'],
            ['Nigeria', 'Abuja'],
            ['Ghana', 'Accra'],
            ['Kenya', 'Nairobi'],
            ['South Africa', 'Pretoria'],
            ['Brazil', 'Brasilia'],
            ['Canada', 'Ottawa'],
            ['Japan', 'Tokyo'],
            ['China', 'Beijing'],
            ['India', 'New Delhi'],
            ['Germany', 'Berlin'],
            ['Italy', 'Rome'],
            ['Spain', 'Madrid'],
            ['Portugal', 'Lisbon'],
            ['Australia', 'Canberra'],
            ['Argentina', 'Buenos Aires'],
            ['United States', 'Washington DC'],
            ['United Kingdom', 'London'],
            ['Russia', 'Moscow'],
            ['Ethiopia', 'Addis Ababa'],
            ['Morocco', 'Rabat'],
            ['Senegal', 'Dakar'],
            ['Cameroon', 'Yaounde'],
            ['Tanzania', 'Dodoma'],
        ];

        $questions = [];
        foreach ($capitals as $index => [$country, $capital]) {
            $questions[] = $this->makeQuestion(
                'What is the capital of ' . $country . '?',
                $capital,
                $this->neighborValues($capitals, $index, 1),
                $index
            );

            $questions[] = $this->makeQuestion(
                $capital . ' is the capital of which country?',
                $country,
                $this->neighborValues($capitals, $index, 0),
                $index + 1
            );
        }

        return array_merge(
            $questions,
            $this->conceptQuestions('Geography', [
                ['Compass', 'an instrument used to find direction'],
                ['Atlas', 'a book or collection of maps'],
                ['Calendar', 'a system for organizing days, weeks, and months'],
                ['Population', 'the total number of people living in an area'],
                ['Agriculture', 'the practice of cultivating crops and rearing animals'],
                ['Transportation', 'the movement of people or goods from place to place'],
                ['Latitude', 'distance north or south of the equator measured in degrees'],
                ['Longitude', 'distance east or west of the prime meridian measured in degrees'],
            ])
        );
    }

    private function comprehensionQuestions(): array
    {
        $marketPassage = 'Passage: A morning market opened early. Traders arranged food, students hurried to school, and officers guided traffic when the narrow road became busy.';
        $libraryPassage = 'Passage: Amara joined a reading club to improve her vocabulary. Each Friday, members discussed a story and wrote a summary.';

        return [
            $this->makeQuestion($marketPassage . ' What opened early?', 'The market.', ['The school.', 'The council office.', 'The library.'], 1),
            $this->makeQuestion($marketPassage . ' Who hurried to school?', 'Students.', ['Officers.', 'Traders.', 'Drivers.'], 2),
            $this->makeQuestion($marketPassage . ' Why did officers guide traffic?', 'The narrow road became busy.', ['The market was closed.', 'Students were absent.', 'The food was sold out.'], 3),
            $this->makeQuestion($marketPassage . ' The word "hurried" means ____.', 'moved quickly', ['slept deeply', 'spoke softly', 'waited calmly'], 4),
            $this->makeQuestion($marketPassage . ' What is the main idea?', 'A busy market morning needed order.', ['A school cancelled classes.', 'A library opened late.', 'Officers sold food.'], 5),
            $this->makeQuestion($libraryPassage . ' Why did Amara join the club?', 'To improve her vocabulary.', ['To sell books.', 'To avoid school.', 'To learn traffic rules.'], 6),
            $this->makeQuestion($libraryPassage . ' When did the members meet?', 'Each Friday.', ['Every morning.', 'Once a year.', 'Only on holidays.'], 7),
            $this->makeQuestion($libraryPassage . ' What did members write?', 'A summary.', ['A traffic report.', 'A shopping list.', 'A timetable.'], 8),
            $this->makeQuestion($libraryPassage . ' What did members discuss?', 'A story.', ['A road map.', 'A football match.', 'A market price.'], 9),
            $this->makeQuestion($libraryPassage . ' Which title best fits?', 'Amara and the Reading Club', ['The Busy Road', 'A Day at the Market', 'The Lost Calculator'], 10),
        ];
    }

    private function mathematicsQuestions(): array
    {
        return [
            $this->makeQuestion('Solve for x: 3x + 7 = 22.', '5', ['4', '6', '7'], 1),
            $this->makeQuestion('Factorize x^2 - 5x + 6.', '(x - 2)(x - 3)', ['(x + 2)(x + 3)', '(x - 1)(x - 6)', '(x + 1)(x - 6)'], 2),
            $this->makeQuestion('Expand (2x - 3)(x + 4).', '2x^2 + 5x - 12', ['2x^2 - 5x - 12', '2x^2 + 8x - 3', '2x^2 + 5x + 12'], 3),
            $this->makeQuestion('If f(x) = 2x^2 - 3x, find f(4).', '20', ['16', '24', '28'], 4),
            $this->makeQuestion('Find the slope of the line through (2, 3) and (6, 11).', '2', ['1', '3', '4'], 5),
            $this->makeQuestion('Find the equation of the line with slope 3 passing through (1, 2).', 'y = 3x - 1', ['y = 3x + 1', 'y = x + 3', 'y = 2x + 3'], 6),
            $this->makeQuestion('Solve simultaneously: x + y = 10 and x - y = 2.', 'x = 6, y = 4', ['x = 4, y = 6', 'x = 5, y = 5', 'x = 8, y = 2'], 7),
            $this->makeQuestion('Solve simultaneously: 2x - y = 7 and x + y = 5.', 'x = 4, y = 1', ['x = 3, y = 2', 'x = 5, y = 0', 'x = 2, y = 3'], 8),
            $this->makeQuestion('What is the sum of the interior angles of a hexagon?', '720 degrees', ['540 degrees', '600 degrees', '900 degrees'], 9),
            $this->makeQuestion('Using pi = 22/7, find the area of a circle with radius 7 cm.', '154 cm^2', ['44 cm^2', '77 cm^2', '308 cm^2'], 10),
            $this->makeQuestion('Find the volume of a cuboid measuring 4 cm by 5 cm by 6 cm.', '120 cm^3', ['90 cm^3', '100 cm^3', '150 cm^3'], 11),
            $this->makeQuestion('A right triangle has legs 9 cm and 12 cm. Find the hypotenuse.', '15 cm', ['14 cm', '16 cm', '21 cm'], 12),
            $this->makeQuestion('Simplify 2^3 x 2^4.', '2^7', ['2^12', '4^7', '2^1'], 13),
            $this->makeQuestion('Simplify a^5 / a^2.', 'a^3', ['a^7', 'a^10', 'a^2'], 14),
            $this->makeQuestion('Find log10(1000).', '3', ['2', '10', '100'], 15),
            $this->makeQuestion('Evaluate sqrt(144) + 3^2.', '21', ['15', '18', '24'], 16),
            $this->makeQuestion('Solve x^2 - 9 = 0.', 'x = 3 or x = -3', ['x = 9 only', 'x = 0 or x = 9', 'x = -9 only'], 17),
            $this->makeQuestion('Find the roots of x^2 - 7x + 10 = 0.', '2 and 5', ['1 and 10', '3 and 4', '-2 and -5'], 18),
            $this->makeQuestion('Find the discriminant of x^2 - 4x + 4 = 0.', '0', ['4', '8', '16'], 19),
            $this->makeQuestion('Find the median of 3, 7, 9, 12, 14.', '9', ['7', '10', '12'], 20),
            $this->makeQuestion('Find the mean of 4, 6, 8, and 10.', '7', ['6', '8', '9'], 21),
            $this->makeQuestion('What is the probability of rolling an even number on a fair die?', '1/2', ['1/3', '2/3', '1/6'], 22),
            $this->makeQuestion('Find the simple interest on 500 at 10 percent per year for 2 years.', '100', ['50', '75', '150'], 23),
            $this->makeQuestion('Find 30 percent of 250.', '75', ['60', '70', '85'], 24),
            $this->makeQuestion('If y varies directly as x and y = 12 when x = 4, find y when x = 7.', '21', ['18', '24', '28'], 25),
            $this->makeQuestion('Find the 10th term of the arithmetic sequence 5, 9, 13, ...', '41', ['37', '45', '49'], 26),
            $this->makeQuestion('Find the next term of the geometric sequence 3, 6, 12, ...', '24', ['18', '21', '30'], 27),
            $this->makeQuestion('Find the sum of the first five terms of 2, 5, 8, 11, ...', '40', ['35', '45', '50'], 28),
            $this->makeQuestion('Find the determinant of the matrix [[2, 3], [1, 4]].', '5', ['7', '8', '11'], 29),
            $this->makeQuestion('If A = [[1, 2], [3, 4]], what is the trace of A?', '5', ['4', '6', '10'], 30),
            $this->makeQuestion('What is sin 30 degrees?', '1/2', ['0', '1', 'sqrt(3)/2'], 31),
            $this->makeQuestion('What is cos 60 degrees?', '1/2', ['0', '1', 'sqrt(3)/2'], 32),
            $this->makeQuestion('What is tan 45 degrees?', '1', ['0', '1/2', 'sqrt(3)'], 33),
            $this->makeQuestion('Convert 3/8 to a decimal.', '0.375', ['0.25', '0.35', '0.625'], 34),
            $this->makeQuestion('Solve |x| = 6.', 'x = 6 or x = -6', ['x = 6 only', 'x = -6 only', 'x = 0 or x = 6'], 35),
            $this->makeQuestion('Expand (x + 2)^2.', 'x^2 + 4x + 4', ['x^2 + 2x + 4', 'x^2 + 4', 'x^2 + 2x + 2'], 36),
            $this->makeQuestion('Rationalize 1 / sqrt(2).', 'sqrt(2) / 2', ['sqrt(2)', '2 / sqrt(2)', '1 / 2'], 37),
            $this->makeQuestion('Write 0.0045 in standard form.', '4.5 x 10^-3', ['4.5 x 10^3', '45 x 10^-3', '0.45 x 10^-2'], 38),
            $this->makeQuestion('Find the gradient of y = -2x + 5.', '-2', ['2', '5', '-5'], 39),
            $this->makeQuestion('Find the y-intercept of y = 4x - 7.', '-7', ['4', '7', '-4'], 40),
            $this->makeQuestion('Find the x-intercept of 2x + 4 = 0.', '-2', ['2', '4', '-4'], 41),
            $this->makeQuestion('Find the perimeter of a rectangle of length 12 cm and width 5 cm.', '34 cm', ['17 cm', '24 cm', '60 cm'], 42),
            $this->makeQuestion('Evaluate 5!.', '120', ['20', '60', '100'], 43),
            $this->makeQuestion('Evaluate 5C2.', '10', ['5', '15', '20'], 44),
            $this->makeQuestion('If A = {1, 2, 3} and B = {3, 4}, find A intersection B.', '{3}', ['{1, 2}', '{4}', '{1, 2, 3, 4}'], 45),
            $this->makeQuestion('What is sin^2(theta) + cos^2(theta)?', '1', ['0', 'sin(theta)', 'cos(theta)'], 46),
            $this->makeQuestion('Solve 4(x - 2) = 20.', '7', ['5', '6', '8'], 47),
            $this->makeQuestion('Convert binary 1010 to decimal.', '10', ['8', '9', '12'], 48),
            $this->makeQuestion('Find the LCM of 12 and 18.', '36', ['6', '24', '72'], 49),
            $this->makeQuestion('Find the HCF of 24 and 36.', '12', ['6', '18', '24'], 50),
        ];
    }

    private function conceptQuestions(string $subject, array $concepts): array
    {
        $questions = [];

        foreach ($concepts as $index => [$term, $definition]) {
            $questions[] = $this->makeQuestion(
                'In ' . $subject . ', which term means ' . $definition . '?',
                $term,
                $this->neighborValues($concepts, $index, 0),
                $index
            );

            $questions[] = $this->makeQuestion(
                'Which statement best describes ' . $term . '?',
                ucfirst($definition) . '.',
                array_map(
                    fn ($value) => ucfirst($value) . '.',
                    $this->neighborValues($concepts, $index, 1)
                ),
                $index + 1
            );
        }

        return $questions;
    }

    private function makeQuestion(string $question, string $correct, array $wrongOptions, int $offset): array
    {
        $options = array_values(array_unique(array_merge([$correct], $wrongOptions)));

        if (count($options) < 4) {
            throw new \RuntimeException('Question needs at least four unique options: ' . $question);
        }

        $options = array_slice($options, 0, 4);
        $shift = $offset % 4;

        for ($i = 0; $i < $shift; $i++) {
            $options[] = array_shift($options);
        }

        $answerIndex = array_search($correct, $options, true);

        return [
            'question' => $question,
            'option_a' => $options[0],
            'option_b' => $options[1],
            'option_c' => $options[2],
            'option_d' => $options[3],
            'answer' => 'option_' . ['a', 'b', 'c', 'd'][$answerIndex],
            'duration' => self::DURATION_SECONDS,
        ];
    }

    private function neighborValues(array $items, int $index, int $column): array
    {
        $values = [];
        $count = count($items);

        for ($step = 1; count($values) < 3 && $step < $count; $step++) {
            $values[] = $items[($index + $step) % $count][$column];
        }

        return $values;
    }

    private function moveGeographyQuestions(): void
    {
        $geography = $this->subjectFor('Geography');

        Question::whereIn('question', [
            'What is the capital of France?',
            'Which city is the capital of France?',
        ])->update(['subject_id' => $geography->id]);
    }

    private function removeDuplicateQuestion(string $questionText): void
    {
        Question::where('question', $questionText)
            ->orderBy('id')
            ->get()
            ->skip(1)
            ->each
            ->delete();
    }

    private function removeRetiredGeneralQuestions(): void
    {
        foreach ($this->retiredGeneralConcepts() as [$term, $definition]) {
            Question::whereIn('question', [
                'In General Knowledge, which term means ' . $definition . '?',
                'Which statement best describes ' . $term . '?',
            ])->delete();
        }
    }

    private function removeOldSampleQuestions(): void
    {
        Question::whereIn('question', [
            'Is this final project',
            'Is this the final project',
        ])->delete();
    }

    private function deactivateEmptyGeneralSubject(): void
    {
        $general = Subject::whereRaw('LOWER(name) = ?', ['general'])->first();

        if ($general && ! $general->questions()->exists()) {
            $general->update(['active' => false]);
        }
    }

    private function retiredGeneralConcepts(): array
    {
        return [
            ['Thermometer', 'an instrument used to measure temperature'],
            ['Compass', 'an instrument used to find direction'],
            ['Calendar', 'a system for organizing days, weeks, and months'],
            ['Dictionary', 'a book or resource that gives word meanings'],
            ['Atlas', 'a book or collection of maps'],
            ['Library', 'a place where books and information resources are kept'],
            ['Microscope', 'an instrument used to view very small objects'],
            ['Telescope', 'an instrument used to view distant objects'],
            ['Stopwatch', 'a device used to measure short intervals of time'],
            ['Calculator', 'a device used to perform mathematical operations'],
            ['Keyboard', 'an input device with keys for typing'],
            ['Printer', 'an output device that produces hard copies'],
            ['Recycling', 'processing used materials so they can be used again'],
            ['Hygiene', 'practices that help maintain health and cleanliness'],
            ['First aid', 'immediate help given to an injured or sick person'],
            ['Road signs', 'symbols that guide and warn road users'],
            ['Traffic light', 'a signal device that controls road movement'],
            ['Renewable energy', 'energy from sources that can be naturally replaced'],
            ['Fossil fuel', 'fuel formed from ancient organic matter'],
            ['Citizenship', 'membership of a country with rights and duties'],
            ['Population', 'the total number of people living in an area'],
            ['Culture', 'the way of life of a group of people'],
            ['Communication', 'the exchange of information between people'],
            ['Agriculture', 'the practice of cultivating crops and rearing animals'],
            ['Transportation', 'the movement of people or goods from place to place'],
        ];
    }

    private function subjectFor(string $name): Subject
    {
        $subject = Subject::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();

        if ($subject) {
            if (! $subject->active) {
                $subject->update(['active' => true]);
            }

            return $subject;
        }

        return Subject::create([
            'name' => $name,
            'slug' => $this->makeUniqueSlug($name),
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
