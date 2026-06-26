@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Question Bank</h1>
            <p class="text-muted mb-0">{{ $totalQuestions }} questions across {{ $subjects->count() }} exam banks.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a class="btn btn-outline-secondary" href="{{ url('/importExportView') }}">Import Questions</a>
            <a class="btn btn-primary" href="{{ route('questions.create') }}">Add Question</a>
        </div>
    </div>

    @if($subjects->isEmpty() && $unassignedQuestions->isEmpty())
        <div class="content-panel">
            <div class="empty-state">
                No exam banks yet. Add a bank or import questions with a subject column.
            </div>
        </div>
    @endif

    @if($subjects->isNotEmpty())
        <section class="content-panel question-bank-tools mb-4">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1">Find A Bank</h2>
                    <p class="text-muted mb-0">Search by bank name or question text, then jump straight to the section.</p>
                </div>
                <div class="dashboard-actions">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseBanks">Collapse All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="expandBanks">Expand All</button>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="questionBankSearch">Search</label>
                <input type="search" class="form-control" id="questionBankSearch"
                    placeholder="Search Biology, Lagos, comprehension, photosynthesis...">
            </div>

            <div class="bank-filter-row mb-3" aria-label="Question bank filters">
                <button type="button" class="btn btn-sm btn-primary active" data-bank-filter="all">All</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bank-filter="academic">Academic</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bank-filter="challenge">Challenge</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bank-filter="active">Active</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bank-filter="inactive">Inactive</button>
            </div>

            <div class="bank-jumpbar">
                @foreach ($subjects as $subject)
                    <a href="#subject-{{ $subject->id }}" data-bank-jump="subject-{{ $subject->id }}">
                        {{ $subject->name }}
                        <span>{{ $subject->questions_count }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @foreach ($subjects as $subject)
        <section class="content-panel mb-4 question-bank-card" id="subject-{{ $subject->id }}"
            data-bank-card
            data-bank-name="{{ strtolower($subject->name) }}"
            data-bank-type="{{ $subject->bank_type }}"
            data-bank-status="{{ $subject->active ? 'active' : 'inactive' }}">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1">{{ $subject->name }}</h2>
                    <p class="text-muted mb-0">
                        {{ $subject->type_label }} &middot;
                        {{ $subject->questions_count }} {{ \Illuminate\Support\Str::plural('question', $subject->questions_count) }}
                        &middot; {{ $subject->active ? 'Active' : 'Inactive' }}
                    </p>
                </div>
                <div class="dashboard-actions">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bank-toggle>Hide</button>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('questions.create', ['subject_id' => $subject->id]) }}">
                        Add Question
                    </a>
                </div>
            </div>

            <div data-bank-body>
                @if($subject->questions->isEmpty())
                    <div class="empty-state">This subject bank is ready, but no questions have been added yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Question</th>
                                    <th>Answer</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subject->questions as $question)
                                    <tr data-question-row>
                                        <td>{{ $question->id }}</td>
                                        <td>{{ $question->question }}</td>
                                        <td>{{ strtoupper(str_replace('option_', '', $question->answer)) }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-sm btn-primary" href="{{ route('questions.edit', $question) }}">Edit</a>
                                            <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    @endforeach

    @if($unassignedQuestions->isNotEmpty())
        <section class="content-panel mb-4">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1">Unassigned</h2>
                    <p class="text-muted mb-0">Questions that need a subject before they can be used cleanly.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unassignedQuestions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->question }}</td>
                                <td>{{ strtoupper(str_replace('option_', '', $question->answer)) }}</td>
                                <td class="text-right">
                                    <a class="btn btn-sm btn-primary" href="{{ route('questions.edit', $question) }}">Assign</a>
                                    <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var search = document.getElementById('questionBankSearch');
            var cards = Array.prototype.slice.call(document.querySelectorAll('[data-bank-card]'));
            var filterButtons = Array.prototype.slice.call(document.querySelectorAll('[data-bank-filter]'));
            var activeFilter = 'all';

            function matchesFilter(card) {
                if (activeFilter === 'all') {
                    return true;
                }

                return card.dataset.bankType === activeFilter || card.dataset.bankStatus === activeFilter;
            }

            function applyFilters() {
                var term = search ? search.value.trim().toLowerCase() : '';

                cards.forEach(function (card) {
                    var bankNameMatches = card.dataset.bankName.indexOf(term) !== -1;
                    var rows = Array.prototype.slice.call(card.querySelectorAll('[data-question-row]'));
                    var matchingRows = 0;

                    rows.forEach(function (row) {
                        var rowMatches = term === '' || bankNameMatches || row.textContent.toLowerCase().indexOf(term) !== -1;
                        row.classList.toggle('d-none', ! rowMatches);

                        if (rowMatches) {
                            matchingRows++;
                        }
                    });

                    var visible = matchesFilter(card) && (term === '' || bankNameMatches || matchingRows > 0);
                    card.classList.toggle('d-none', ! visible);
                });
            }

            if (search) {
                search.addEventListener('input', applyFilters);
            }

            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activeFilter = button.dataset.bankFilter;

                    filterButtons.forEach(function (item) {
                        item.classList.remove('active', 'btn-primary');
                        item.classList.add(item.dataset.bankFilter === 'active' || item.dataset.bankFilter === 'inactive' ? 'btn-outline-secondary' : 'btn-outline-primary');
                    });

                    button.classList.add('active', 'btn-primary');
                    button.classList.remove('btn-outline-primary', 'btn-outline-secondary');
                    applyFilters();
                });
            });

            document.querySelectorAll('[data-bank-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var card = button.closest('[data-bank-card]');
                    var body = card.querySelector('[data-bank-body]');
                    var hidden = body.classList.toggle('d-none');
                    button.textContent = hidden ? 'Show' : 'Hide';
                });
            });

            var collapse = document.getElementById('collapseBanks');
            var expand = document.getElementById('expandBanks');

            if (collapse) {
                collapse.addEventListener('click', function () {
                    document.querySelectorAll('[data-bank-body]').forEach(function (body) {
                        body.classList.add('d-none');
                    });
                    document.querySelectorAll('[data-bank-toggle]').forEach(function (button) {
                        button.textContent = 'Show';
                    });
                });
            }

            if (expand) {
                expand.addEventListener('click', function () {
                    document.querySelectorAll('[data-bank-body]').forEach(function (body) {
                        body.classList.remove('d-none');
                    });
                    document.querySelectorAll('[data-bank-toggle]').forEach(function (button) {
                        button.textContent = 'Hide';
                    });
                });
            }
        });
    </script>
@endpush
