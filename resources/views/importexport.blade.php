@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Import Questions</h1>
            <p class="text-muted mb-0">Upload CSV or Excel rows into subject question banks.</p>
        </div>
        <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary mt-3 mt-md-0">Question Bank</a>
    </div>

    <section class="content-panel">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1">Bulk Upload</h2>
                <p class="text-muted mb-0">New subject names in the file will create new banks automatically.</p>
            </div>
        </div>

        <p class="text-muted">
            Required headings:
            <code>subject</code>, <code>question</code>, <code>option_a</code>,
            <code>option_b</code>, <code>option_c</code>, <code>option_d</code>,
            and <code>answer</code>. The answer may be <code>A</code>, <code>B</code>,
            <code>C</code>, <code>D</code>, or <code>option_a</code> through <code>option_d</code>.
        </p>

        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="file">Question File</label>
                <input type="file" id="file" name="file" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
            </div>

            <button class="btn btn-success">Import Questions</button>
        </form>
    </section>
@endsection
