<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Bibleversion;
use Illuminate\Http\Request;

class BibleversionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all versions
        $bibleversions = Bibleversion::get();

        return view('admin.bibleversions', [
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function show(Bibleversion $bibleversion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function edit(Bibleversion $bibleversion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bibleversion $bibleversion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bibleversion  $bibleversion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bibleversion $bibleversion)
    {
        //
    }
}
