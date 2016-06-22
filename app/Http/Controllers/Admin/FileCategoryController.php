<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreFileCategoryRequest;
use App\Http\Controllers\Controller;

use App\Models\FileCategory;
use App\Models\File;


class FileCategoryController extends Controller
{


    /**
     * define view names
     */
    protected $view_all = 'admin.file_categories';
    protected $view_idx = 'admin.file_categories.index';
    protected $view_one = 'admin.file_category';



    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:editor', ['except' => ['index', 'show']]);
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $file_categories = FileCategory::get();

        $heading = 'Manage File Categories';
        return view( $this->view_all, array('file_categories' => $file_categories, 'heading' => $heading) );
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view($this->view_one);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFileCategoryRequest $request)
    {
        //
        FileCategory::create($request->all());
        $status = 'New File Category added.';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $status]);
    }







    /**
     * Display linked records of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // redirect to Files List View with active filter on category
        return \Redirect::route('cspot.files')
                        ->with(['bycategory' => $id]);
    }




    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // find a single resource by ID
        $output = FileCategory::find($id);
        if ($output) {
            return view( $this->view_one, array('file_category' => $output ) );
        }
        //
        $message = 'Error! File Category with id "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreFileCategoryRequest $request, $id)
    {
        // was there any change?
        $output = FileCategory::find($id);
        if ($request->input('name') == $output->name) 
        {
            return \Redirect::route($this->view_idx)
                        ->with(['status' => 'no change']);
        }
        // get this Role
        FileCategory::where('id', $id)
                ->update($request->except(['_method','_token']));

        $message = 'File Category with id "' . $id . '" updated';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // default values cannot be deleted
        if ($id < 1) {
            $message = 'Error! The basic File Categories (IDs 0 and 1) cannot be deleted!';
            return \Redirect::route($this->view_idx)
                            ->with(['status' => $message]);            
        }
        // find a single resource by ID
        $output = FileCategory::find($id);
        if ($output) {
            $output->delete();
            $message = 'File Category with id "' . $id . '" deleted.';
            return \Redirect::route($this->view_idx)
                            ->with(['status' => $message]);
        }
        //
        $message = 'Error! File Category with ID "' . $id . '" not found';
        return \Redirect::route($this->view_idx)
                        ->with(['status' => $message]);
    }
}
