<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Createmath;
use App\Models\Createhistory;
use App\Models\Math;
use App\Models\History;
use App\Models\Mathanswer;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function exam()
    {
        return view('exam');
    }

    public function examcreate()
    {
        return view('new');
    }

    public function exammath()
    {
        // $id = $request->input('id');
        $id = 4;
        // $findcourse = Math::where('id','=',$id)->value('id');
        // $findtime = Math::where('id','=',$id)->value('id');
        $course = Math::where('id','=',$id)->value('Course');
        $time = Math::where('id','=',$id)->value('time');
        $questions = Createmath::where('math_id', $id)->get();
        return view('/examm/math')->with('createmaths', $questions)->with('Course', $course)->with('time', $time);
    }

    public function examhistory()
    {
        // $id = $request->input('id');
        $id = 2;
        // $findcourse = Math::where('id','=',$id)->value('id');
        // $findtime = Math::where('id','=',$id)->value('id');
        $course = History::where('id','=',$id)->value('Course');
        $time = History::where('id','=',$id)->value('time');
        $questions = Createhistory::where('history_id', $id)->get();
        return view('/examm/history')->with('createhistories', $questions)->with('Course', $course)->with('time', $time);
    }

    public function math()
    {
        return view('questions/math');
    }

    public function history()
    {
        return view('questions/history');
    }

    public function show()
    {
        return view('show');
    }
}
