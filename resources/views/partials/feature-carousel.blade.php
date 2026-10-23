@php($isPublicCarousel = !empty($public))
<section class="feature-carousel {{ $isPublicCarousel ? 'feature-carousel--public' : '' }}" data-feature-carousel role="region" aria-roledescription="carousel" aria-label="Explore CrazyExam">
    <div class="feature-carousel-controls" data-carousel-controls hidden>
        <div class="feature-carousel-dots" aria-label="Choose a feature">
            @foreach(['Practice exams', 'Lagos Rush', 'Subject Rush', 'Word Scramble', 'Picture Puzzles'] as $slideName)
                <button type="button" data-carousel-go="{{ $loop->index }}" aria-label="Show {{ $slideName }}" @if($loop->first) aria-current="true" @endif><span aria-hidden="true"></span></button>
            @endforeach
        </div>
        <span class="feature-carousel-count" data-carousel-count aria-hidden="true">1 / 5</span>
        <div class="feature-carousel-buttons">
            <button type="button" data-carousel-previous aria-label="Previous feature">@include('partials.exam-icon', ['icon' => 'back'])</button>
            <button type="button" data-carousel-toggle aria-label="Pause slideshow"><span data-carousel-play-icon aria-hidden="true">Ⅱ</span></button>
            <button type="button" data-carousel-next aria-label="Next feature">@include('partials.exam-icon', ['icon' => 'arrow'])</button>
        </div>
    </div>
    <div class="feature-carousel-viewport">
        <div class="feature-slide feature-slide--practice" data-feature-slide role="group" aria-roledescription="slide" aria-label="1 of 5: Practice exams">
            <div class="feature-slide-grid">
                <div class="feature-slide-copy">
                    <p class="feature-slide-kicker">Practice with purpose</p>
                    <h1>{{ $isPublicCarousel ? 'Build confidence before the real exam.' : 'Ready for your next practice run?' }}</h1>
                    <p>Choose one subject or mix several together. Set your questions and time, then learn from every result.</p>
                    <div class="feature-slide-actions">
                        <a href="{{ route('exam.start') }}" class="btn btn-success">{{ $isPublicCarousel ? 'Start Exam' : 'Start New Exam' }} @include('partials.exam-icon', ['icon' => 'arrow'])</a>
                        @if($isPublicCarousel && ! auth()->check())
                            <a href="{{ route('register') }}" class="btn btn-outline-light">Create Account</a>
                        @else
                            <a href="{{ $isPublicCarousel ? route('home') : route('exam.results') }}" class="btn btn-outline-light">{{ $isPublicCarousel ? 'Dashboard' : 'View Results' }}</a>
                        @endif
                    </div>
                    <p class="feature-slide-note">Your questions. Your time. Your pace.</p>
                </div>
                <div class="feature-practice-preview" aria-hidden="true">
                    <span class="feature-preview-eyebrow">MAKE IT YOURS</span>
                    @include('partials.exam-icon', ['icon' => 'book'])
                    <div><strong>10 questions</strong><span>5 minutes? Take your time.</span></div>
                    <div><strong>100 questions</strong><span>1 minute? Challenge yourself.</span></div>
                </div>
            </div>
        </div>
        <div class="feature-slide feature-slide--lagos" data-feature-slide role="group" aria-roledescription="slide" aria-label="2 of 5: Lagos Rush" hidden inert aria-hidden="true">
            @include('partials.lagos-rush-hero', ['public' => false, 'embedded' => true])
        </div>
        <div class="feature-slide feature-slide--rush" data-feature-slide role="group" aria-roledescription="slide" aria-label="3 of 5: Subject Rush" hidden inert aria-hidden="true">
            <div class="feature-slide-grid">
                <div class="feature-slide-copy">
                    <p class="feature-slide-kicker">Race the clock</p>
                    <h2>One subject.<br>How fast are you?</h2>
                    <p>Pick any subject and answer as many as you can. Every tap gets marked immediately, with sounds to match.</p>
                    <div class="feature-slide-actions"><a href="{{ route('exam.start', ['mode' => 'rush']) }}" class="btn btn-success">Play Rush @include('partials.exam-icon', ['icon' => 'arrow'])</a></div>
                    <p class="feature-slide-note">Choose 15–180 seconds. Keep your first answer sharp.</p>
                </div>
                <div class="feature-rush-preview" aria-hidden="true"><span class="feature-preview-eyebrow">YOUR NEXT CHALLENGE</span><strong>01:00</strong><span>How many can you get right?</span>@include('partials.exam-icon', ['icon' => 'bolt'])</div>
            </div>
        </div>
        <div class="feature-slide feature-slide--words" data-feature-slide role="group" aria-roledescription="slide" aria-label="4 of 5: Word Scramble" hidden inert aria-hidden="true">
            <div class="feature-slide-grid">
                <div class="feature-slide-copy">
                    <p class="feature-slide-kicker">Word Scramble</p>
                    <h2>A little mixed up?<br>Sort it out.</h2>
                    <p>One clue, a jumble of letters and a ticking clock. Find the word, type your answer and keep going.</p>
                    <div class="feature-slide-actions"><a href="{{ route('games.start', 'word-scramble') }}" class="btn btn-success">Unscramble Words @include('partials.exam-icon', ['icon' => 'arrow'])</a></div>
                    <p class="feature-slide-note">Instant feedback. Skip if you’re stuck.</p>
                </div>
                <div class="feature-word-preview" aria-hidden="true"><span class="feature-preview-eyebrow">PUT THE LETTERS IN ORDER</span><div>@foreach(str_split('RTEAKM') as $letter)<span>{{ $letter }}</span>@endforeach</div><p>Clue: A place to buy and sell.</p></div>
            </div>
        </div>
        <div class="feature-slide feature-slide--pictures" data-feature-slide role="group" aria-roledescription="slide" aria-label="5 of 5: Picture Puzzles" hidden inert aria-hidden="true">
            <div class="feature-slide-grid">
                <div class="feature-slide-copy">
                    <p class="feature-slide-kicker">Picture Puzzles</p>
                    <h2>Look closer.<br>What does it say?</h2>
                    <p>The words, shapes and positions hide a familiar phrase. Spot it before the clock runs out.</p>
                    <div class="feature-slide-actions"><a href="{{ route('games.start', 'picture-puzzles') }}" class="btn btn-success">Solve Picture Puzzles @include('partials.exam-icon', ['icon' => 'arrow'])</a></div>
                    <p class="feature-slide-note">Say what you see. Give your brain a quick workout.</p>
                </div>
                <div class="feature-picture-preview" aria-hidden="true"><span class="feature-preview-eyebrow">A PHRASE IS HIDING HERE</span><img src="{{ asset('puzzles/rebus/puzzle-01.svg') }}" alt="" width="600" height="300"></div>
            </div>
        </div>
    </div>
    <span class="sr-only" data-carousel-status role="status" aria-live="polite"></span>
    <noscript><p class="feature-carousel-fallback">Also try <a href="{{ route('exam.lagos-rush') }}">Lagos Rush</a>, <a href="{{ route('games.index') }}">Word Scramble and Picture Puzzles</a>.</p></noscript>
</section>
