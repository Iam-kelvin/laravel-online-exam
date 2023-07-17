<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\BulkExport;
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
    public function import(Request $request, ) 
    {
        Excel::import(new BulkImport,request()->file('file'));

        $request->session()->flash('success', 'Bulk Question Uploaded');

        return back();
    }
}
