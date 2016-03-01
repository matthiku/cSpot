<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

# (C) 2016 Matthias Kuhs, Ireland

Route::group(['middleware' => 'web'], function () {
    //

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/home', 'HomeController@index');

    // all authorization routes
    Route::auth();
    // confirm registration route from registration email
    Route::get('register/confirm/{token}', 'Auth\AuthController@confirmEmail');


    // Social (OAuth) authorization
    $s = 'social.';
    Route::get('/social/redirect/{provider}',   ['as' => $s . 'redirect',   'uses' => 'Auth\AuthController@getSocialRedirect']);
    Route::get('/social/handle/{provider}',     ['as' => $s . 'handle',     'uses' => 'Auth\AuthController@getSocialHandle']);


    // API route to compile bible references
    Route::get('bible/books',                           'Cspot\BibleController@books'); // get all books
    Route::get('bible/books/all/chapters',              'Cspot\BibleController@allChapters'); // get chapter numbers of ALL books
    Route::get('bible/books/all/verses',                'Cspot\BibleController@allVerses'); // get chapter numbers of ALL books
    Route::get('bible/books/{book}',                    'Cspot\BibleController@chapters'); // get chapter numbers of a book
    Route::get('bible/books/{book}/chapters/{chapter}', 'Cspot\BibleController@verses'); // get verse numbers of a chapter
    // get bible texts
    Route::get('bible/text/{version}/{book}/{chapter}/','Cspot\BibleController@getChapter'); // get bible text
    Route::get('bible/passage/{version}/{book}/{chapter}/{verseFrom}/{verseTo}/','Cspot\BibleController@getBibleText'); // get bible passage

});



/*
|--------------------------------------------------------------------------
|    Routes for the core application
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'cspot', 'middleware' => ['web', 'auth']], function() {

    // PLANS

    // show only upcoming service plans
    Route::get('plans/future', ['as'=>'future', 'uses'=>'Cspot\PlanController@future']);
    // show only next Sunday Service plan
    Route::get('plans/next',   ['as'=>'next',   'uses'=>'Cspot\PlanController@nextSunday']);

    // basic CRUD resources for plans
    Route::resource('plans',                        'Cspot\PlanController');
    // allow DELETE via the GET method
    Route::get('plans/{plan_id}/delete',            'Cspot\PlanController@destroy');    
    // show filtered resources (only future by default!)
    Route::get('plans/by_user/{user_id}/{all?}',    'Cspot\PlanController@by_user');    
    Route::get('plans/by_type/{type_id}/{all?}',    'Cspot\PlanController@by_type');    
    // update (append) the note for a plan
    Route::put('plans/{plan_id}/addNote', ['as'=>'addNote', 'uses'=>'Cspot\PlanController@addNote']);

    // send an email reminder to a leader or teacher for a plan
    Route::get('plans/{plan_id}/remind/{user_id}', ['as'=>'sendReminder', 'uses'=>'Cspot\PlanController@sendReminder']);
    

    // ITEMS

    // show form of next or previous item for a plan
    Route::get('plans/{plan_id}/items/{item_id}/go/{direction}/{chords?}',    'Cspot\ItemController@next');
    // show form to create a new item for a plan
    Route::get('plans/{plan_id}/items/create/before/{item_id}',                     'Cspot\ItemController@create');    
    // insert new item with song_id 
    Route::get('plans/{plan_id}/items/store/seq_no/{seq_no}/song/{song_id}/{moreItems?}/{beforeItem?}',     'Cspot\ItemController@insertSong');    
    // update item with new song_id 
    Route::get('plans/{plan_id}/items/update/item/{item_id}/song/{song_id}',     'Cspot\ItemController@updateSong');    
    // show form to create a new item for a plan
    Route::get('plans/{plan_id}/items/create/{seq_no}',             '       Cspot\ItemController@create');    
    // show form to update a new item for a plan
    Route::get('plans/{plan_id}/items/{item_id}/edit',                  'Cspot\ItemController@edit');    
    // presentation view of a plan
    Route::get('items/{items}/{present?}',                          'Cspot\ItemController@show');

    // generic item resource routes
    Route::resource('items', 'Cspot\ItemController');

    // MOVE the specified resource up or down in the list of items related to a plan
    Route::get('items/{items}/move/{direction}',        'Cspot\ItemController@move');
    // specific (soft) delete route using 'get' method
    Route::get('items/{items}/delete',                  'Cspot\ItemController@destroy');    
    // permanently delete an item
    Route::get('items/{items}/permDelete',              'Cspot\ItemController@permDelete');    
    // restor a soft-delted item
    Route::get('items/{items}/restore',                 'Cspot\ItemController@restore');    
    // delete all trashed items of a plan
    Route::get('plans/{plan_id}/items/trashed/restore', 'Cspot\ItemController@restoreAllTrashed');    
    // delete all trashed items of a plan
    Route::get('plans/{plan_id}/items/trashed/delete',  'Cspot\ItemController@deleteAllTrashed');    

    // basic songs processing
    Route::resource('songs',            'Cspot\SongController');
    // specific delete route using 'get' method
    Route::get('songs/{songs}/delete',  'Cspot\SongController@destroy');    

});




/*
|--------------------------------------------------------------------------
| Routes for users with special rights
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => ['web']], function() {

    // admin only: CRUD for users
    Route::resource('users', 'Admin\UserController');    
    Route::resource('roles', 'Admin\RoleController');    
    Route::resource('types', 'Admin\TypeController');    
    Route::resource('default_items', 'Admin\DefaultItemController');    
    // as forms cannot use DELETE method, we implement it as GET
    Route::get('users/{users}/delete', 'Admin\UserController@destroy');    
    Route::get('roles/{roles}/delete', 'Admin\RoleController@destroy');    
    Route::get('types/{types}/delete', 'Admin\TypeController@destroy');    
    Route::get('default_items/{default_items}/delete', 'Admin\DefaultItemController@destroy');    

});
