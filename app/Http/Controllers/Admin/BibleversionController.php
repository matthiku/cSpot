<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreBibleversionRequest;

use App\Models\Bibleversion;
use App\Models\Bible;
use Illuminate\Http\Request;

class BibleversionController extends Controller
{


    /**
     * define view names
     */
    protected $index    = 'bibleversions.index';
    protected $view_idx = 'admin.bibleversions';
    protected $view_one = 'admin.bibleversion';
    protected $create   = 'bibleversions.create';


    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:editor', ['except' => ['index', 'show']]);
        $this->middleware('role:administrator', ['only' => ['destroy', 'create']]);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all versions
        $bibleversions = Bibleversion::get();

        return view($this->view_idx, [
            'bibleversions' => $bibleversions, 
            'heading'   => 'Show Bible Versions'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view($this->view_one, [
            'heading'   => 'Add new Bible Version Name'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBibleversionRequest $request)
    {
        $status = "You must provide a name!";
        // check if name was given
        if (! $request->has('name'))
            return \Redirect::route($this->create)
                        ->with(['status' => $status, 'heading' => 'Add a new Bible Version Name']);                        

        Bibleversion::create($request->all());
        
        $status = 'New Bible Version added: '.$request->name;
        return \Redirect::route($this->index)
                        ->with(['status' => $status]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function show(Bibleversion $bibleversion)
    {
        // 'show' not really used
        return redirect()->back()->withInput();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // get this version
        $bibleversion = Bibleversion::find($id);

        return view($this->view_one, [
            'bibleversion' => $bibleversion, 
            'heading'   => 'Edit Bible Version Name'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function update(StoreBibleversionRequest $request, $id)
    {
        $bibleversion = Bibleversion::find($id);
        $bibleversion->name = $request->name;
        $bibleversion->copyright = $request->copyright;
        $bibleversion->save();

        $status = 'New Bible Version updated: '.$request->name;
        return \Redirect::route($this->index)
                        ->with(['status' => $status]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // first check if there are bible texts using this version
        $status = "You cannot delete a Version of a Bible that is still being used in the storage!";
        $bible = Bible::where('bibleversion_id', $id)->first();
        if ($bible)
            return \Redirect::route($this->index)
                        ->with(['status' => $status, 'heading' => 'Show Bible Versions']);                        

        // now delete this version name
        $bibleversion = Bibleversion::find($id);
        $status = "Bible version deleted: ".$bibleversion->name;
        $bibleversion = Bibleversion::destroy($id);
        return \Redirect::route($this->index)
                        ->with(['status' => $status]);
    }
}
