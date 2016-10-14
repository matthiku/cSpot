<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use Auth;


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
     * @return Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the welcome screen after a successful logon
     *
     * or another screen the user chose as his start page
     *
     * @return Response
     */
    public function welcome()
    {
        if (Auth::user()->startPage != '') {
            return redirect( Auth::user()->startPage );
        }
        elseif (Auth::user()->hasRole('musician')) {
            return redirect( route('next') );
        }
        return view('welcome');
    }
}
