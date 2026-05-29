<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\BulkImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * 
    */
    public function importExportView()
    {
        return view('importexport');
    }
    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        Excel::import(new BulkImport, $validated['file']);

        $request->session()->flash('success', 'Question banks updated successfully.');

        return back();
    }
}
