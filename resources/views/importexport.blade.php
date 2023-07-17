@extends('layouts.app')

@section('content')
    <div class="card bg-light mt-3">
        <div class="card-header">
            Import Bulk Questions 
        </div>
        <div class="card-body">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success">Import Bulk Data</button>
                <a class="btn btn-warning" href="{{ route('export') }}">Export Bulk Data</a>
            </form>
        </div>
    </div>
@endsection
