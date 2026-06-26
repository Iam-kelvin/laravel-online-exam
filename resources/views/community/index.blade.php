@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Community</h1>
            <p class="text-muted mb-0">Request new state quizzes, suggest challenge banks, or talk about what should come next.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="content-panel">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1">Start A Thread</h2>
                        <p class="text-muted mb-0">Ask for your state, topic, or challenge idea.</p>
                    </div>
                </div>

                <form action="{{ route('community.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="{{ old('title') }}" maxlength="120" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Type</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="request" {{ old('category', 'request') === 'request' ? 'selected' : '' }}>Request</option>
                            <option value="discussion" {{ old('category') === 'discussion' ? 'selected' : '' }}>Discussion</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="body">Details</label>
                        <textarea class="form-control" id="body" name="body" rows="5" maxlength="2000" required>{{ old('body') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            @forelse($posts as $post)
                <article class="content-panel community-post mb-3">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <span class="badge badge-{{ $post->category === 'request' ? 'info' : 'secondary' }}">
                                {{ ucfirst($post->category) }}
                            </span>
                            <h2 class="h5 mt-2 mb-1">{{ $post->title }}</h2>
                            <p class="text-muted mb-2">
                                {{ $post->user->publicName() }} &middot; {{ $post->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <span class="badge badge-success align-self-start">{{ ucfirst($post->status) }}</span>
                    </div>

                    <p>{{ $post->body }}</p>

                    <div class="community-comments">
                        @forelse($post->comments as $comment)
                            <div class="community-comment">
                                <strong>{{ $comment->user->publicName() }}</strong>
                                <span class="text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                <p class="mb-0">{{ $comment->body }}</p>
                            </div>
                        @empty
                            <p class="text-muted small mb-2">No replies yet.</p>
                        @endforelse
                    </div>

                    <form action="{{ route('community.comments.store', $post) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="body" class="form-control" maxlength="1000"
                                placeholder="Add a reply" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-primary">Reply</button>
                            </div>
                        </div>
                    </form>
                </article>
            @empty
                <div class="content-panel empty-state">
                    No community posts yet. Start with the first request.
                </div>
            @endforelse

            {{ $posts->links() }}
        </div>
    </div>
@endsection
