<?php

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Plan;
use App\Models\History;


class HistoryController extends Controller
{

    // show all records
    public function index(Request $request)
    {
    	if ($request->has('plan_id')) {

    		$heading = "Show History for a Specific Plan";

    		$histories = History::with('plan', 'user')
    				->where('plan_id', $request->plan_id)
    				->get();
    	}
    	else {    		

    		$heading = 'History of all changes to Event Plans';

    		$histories = History::with('plan', 'user')->get();
    	}

    	return view('cspot.history')->with(['heading'=>$heading, 'histories'=>$histories]);
    }
}
