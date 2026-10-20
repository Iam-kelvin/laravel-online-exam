@if($question->question_type === 'scramble')
    <div class="scramble-letters" aria-label="Scrambled letters">{{ implode(' ', str_split($question->prompt_data['letters'])) }}</div>
    <p class="scramble-clue">Clue: {{ $question->prompt_data['hint'] }}</p>
@elseif($question->question_type === 'rebus')
    <img class="rebus-image" src="{{ asset($question->prompt_data['image']) }}" alt="{{ $question->prompt_data['alt'] }}">
@endif
