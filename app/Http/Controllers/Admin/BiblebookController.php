<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use DB;

use App\Models\Biblebook;
use App\Models\Bible;
use Illuminate\Http\Request;

class BiblebookController extends Controller
{


    /**
     * define view names
     */
    protected $index    = 'biblebooks.index';
    protected $view_idx = 'admin.biblebooks';
    protected $view_one = 'admin.biblebook';
    protected $create   = 'biblebooks.create';



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // get all books
        $biblebooks = Biblebook::get();

        return view($this->view_idx, [
            'biblebooks' => $biblebooks, 
            'request'    => $request, 
            'heading'    => 'Show Bible Books'
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
            'heading'   => 'Add new Bible Book Name'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = "You must provide a name!";
        // check if name was given
        if (! $request->has('name'))
            return \Redirect::route($this->create)
                        ->with(['status' => $status, 'heading' => 'Add a new Bible Book Name']);                        

        Biblebook::create($request->all());
        $status = 'New Bible Book added: '.$request->name;
        return \Redirect::route($this->index)
                        ->with(['status' => $status]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Biblebook  $biblebook
     * @return \Illuminate\Http\Response
     */
    public function show(Biblebook $biblebook)
    {
        // 'show' not really used
        return redirect()->back()->withInput();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Biblebook  $biblebook
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // get this book
        $biblebook = Biblebook::find($id);

        return view($this->view_one, [
            'biblebook' => $biblebook, 
            'heading'   => 'Edit Bible Book Name'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Biblebook  $biblebook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $status = "You must provide a name!";
        // check if name was given
        if (! $request->has('name'))
            return \Redirect::route($this->edit, ['id'=>$id])
                        ->with(['status' => $status, 'heading' => 'Add a new Bible Book Name']);                        

        $biblebook = Biblebook::find($id);
        $biblebook->name = $request->name;
        $biblebook->save();

        $status = 'New Bible Book updated: '.$request->name;
        return \Redirect::route($this->index)
                        ->with(['status' => $status]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Biblebook  $biblebook
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // first check if there are bible texts using this book
        $status = "You cannot delete a Book of a Bible that is still being used in the storage!";
        $bible = Bible::where('biblebook_id', $id)->first();
        if ($bible)
            return \Redirect::route($this->index)
                        ->with(['status' => $status, 'heading' => 'Show Bible Books']);                        

        // now delete this book name
        $biblebook = Biblebook::find($id);
        $status = "Bible book deleted: ".$biblebook->name;
        $biblebook = Biblebook::destroy($id);
        return \Redirect::route($this->index)
                        ->with(['status' => $status]);
    }
}
