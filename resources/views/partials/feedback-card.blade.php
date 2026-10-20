<aside class="card feedback-card mt-4" aria-label="Share feedback">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
        <div class="mr-3">
            <h2 class="h5 mb-1">How was that?</h2>
            <p class="text-muted mb-0">Found a tricky question, spotted a mistake or have an idea? Tell us in the community.</p>
        </div>
        <a href="{{ route('community.index', ['feedback' => $attempt->examName()]) }}#community-post-form" class="btn btn-success mt-3 mt-md-0">Drop feedback &rarr;</a>
    </div>
</aside>
