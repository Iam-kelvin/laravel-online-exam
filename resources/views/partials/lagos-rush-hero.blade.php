<section class="lagos-feature {{ !empty($public) ? 'lagos-feature--public' : '' }}" aria-labelledby="lagosRushHeading">
    <div class="lagos-feature-grid">
        <div class="lagos-feature-copy">
            <p class="lagos-feature-kicker">@include('partials.exam-icon', ['icon' => 'bolt']) Lagos Rush <span>Street knowledge. On the clock.</span></p>
            <h1 id="lagosRushHeading">Think you know Lagos?<span>Oya, prove it.</span></h1>
            <p class="lagos-feature-description">Bus stops, markets, old Lagos. How many can you get right in 60 seconds?</p>
            <div class="lagos-feature-actions">
                <a href="{{ route('exam.lagos-rush') }}" class="btn lagos-play-button" data-lagos-launch>Play Lagos Rush @include('partials.exam-icon', ['icon' => 'arrow'])</a>
                <a href="{{ route('exam.start') }}" class="btn lagos-secondary-button">Explore other exams</a>
            </div>
            <p class="lagos-feature-note">Start with 60 seconds. Change the time before you play.</p>
            <ul class="lagos-feature-perks" aria-label="Lagos Rush features">
                <li>@include('partials.exam-icon', ['icon' => 'clock']) Beat the clock</li>
                <li>@include('partials.exam-icon', ['icon' => 'check']) Instant marking</li>
                <li>@include('partials.exam-icon', ['icon' => 'spark']) Meme reactions</li>
            </ul>
        </div>
        <div class="lagos-feature-scene" aria-hidden="true">
            <div class="lagos-scene-top"><span class="lagos-scene-ticket">LAGOS RUSH</span><span class="lagos-scene-clock">60<span>SECONDS</span></span></div>
            <img class="lagos-danfo" src="{{ asset('images/lagos-rush.svg') }}" alt="" width="620" height="340">
            <div class="lagos-route-stops"><span>OSHODI</span><span>YABA</span><span>CMS</span><span>YOUR TURN</span></div>
            <p class="lagos-scene-challenge">Your city. Your bragging rights.</p>
        </div>
    </div>
</section>
