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

    Route::get('/', 'HomeController@welcome');
    Route::get('/home', ['as'=>'home', 'uses'=>'HomeController@index']);

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

    // user confirms his partizipation with TOKEN (no login needed!)
    Route::get( 'cspot/plans/{plan_id}/team/{team_id}/confirm/{token}',                'Cspot\TeamController@confirm');

});





/*
|--------------------------------------------------------------------------
|    Routes for the core application
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'cspot', 'middleware' => ['web', 'auth']], function() {



    /*
         PLANS
    */

    // show next Sunday's Service plan
    Route::get('plans/next',                              ['as'=>'next',   'uses'=>'Cspot\PlanController@nextSunday']);

    // basic CRUD resources for plans, but without the simple GET method
    Route::resource('plans',                                                       'Cspot\PlanController');

    // show one plan for the selected date
    Route::get('plans/by_date/{date}',                                             'Cspot\PlanController@by_date');    

    // allow DELETE via the GET method
    Route::get('plans/delete/{plan_id}',                                           'Cspot\PlanController@destroy');    
    // update (append) the note for a plan
    Route::put('plans/{plan_id}/addNote', ['as'=>'addNote',               'uses'=> 'Cspot\PlanController@addNote']);

    // send an email reminder to a leader or teacher for a plan
    Route::get('plans/{plan_id}/remind/{user_id}', ['as'=>'sendReminder', 'uses'=> 'Cspot\PlanController@sendReminder']);

    // store client-rendered presentation slides locally for other users to download
    Route::post( 'plan/{plan_id}/cache',                                            'Cspot\PlanController@postCache');
    Route::get(  'plan/{plan_id}/cache',                                            'Cspot\PlanController@getCache');
    Route::post( 'plan/{plan_id}/cache/delete',                                     'Cspot\PlanController@deleteCache');
    

    /*
        PLAN TEAMS
    */
    // Manage team for a service plan
    Route::get( 'plans/{plan_id}/team', ['as' => 'team.index', 'uses' => 'Cspot\TeamController@index']);
    Route::post('plans/{plan_id}/team', ['as' => 'team.store', 'uses' => 'Cspot\TeamController@store']);
    // user announces his availability for a certain plan
    Route::get( 'plans/{plan_id}/team/available/{bool}',                 'Cspot\TeamController@available');
    // shortcut to add all musicians to a plan
    Route::get( 'plans/{plan_id}/team/addAllMusicians',                  'Cspot\TeamController@addAllMusicians');
    // show the form to edit an existing team member
    Route::get( 'plans/{plan_id}/team/{team_id}/edit',                   'Cspot\TeamController@edit');
    // update an existing team member
    Route::post('plans/{plan_id}/team/{team_id}/update',                 'Cspot\TeamController@update');
    // delete an existing team member
    Route::get( 'plans/{plan_id}/team/{team_id}/delete',                 'Cspot\TeamController@destroy');
    // send email to user to request his partizipation
    Route::get( 'plans/{plan_id}/team/{team_id}/sendrequest',            'Cspot\TeamController@sendrequest');
    // user confirms his partizipation
    Route::get( 'plans/{plan_id}/team/{team_id}/confirm',                'Cspot\TeamController@confirm');


    /*
        RESOURCES
    */
    // Manage resources for a service plan
    Route::get( 'plans/{plan_id}/resource', ['as' => 'resource.index', 'uses' => 'Cspot\ResourceController@index']);
    Route::post('plans/{plan_id}/resource', ['as' => 'resource.store', 'uses' => 'Cspot\ResourceController@store']);
    // show the form to edit an existing resource
    //Route::get( 'plans/{plan_id}/resource/{resource_id}/edit',                   'Cspot\ResourceController@edit');
    // update an existing resource
    Route::post('api/plans/resource/update',                                     'Cspot\ResourceController@APIupdate');
    // delete an existing resource
    Route::get( 'plans/{plan_id}/resource/{resource_id}/delete',                 'Cspot\ResourceController@destroy');


    /*
         ITEMS
     */

    // update a specific item (this is usually called from a form)
    Route::put('items/{item_id}',       ['as'=>'cspot.items.update', 'uses'=>'Cspot\ItemController@update']);    
    Route::post('items',                ['as'=>'cspot.items.store',  'uses'=>'Cspot\ItemController@store']);    
    // add song directly from the song list to a plan
    Route::get('plans/{plan_id}/addsong/{song_id}',                          'Cspot\ItemController@addSong');
    // show form of next or previous item for a plan
    Route::get('plans/{plan_id}/items/{item_id}/go/{direction}/{chords?}',    'Cspot\ItemController@next');
    // show form to create a new item for a plan
    Route::get('plans/{plan_id}/items/create/before/{item_id}',               'Cspot\ItemController@create');    
    // insert new item with song_id 
    Route::get('plans/{plan_id}/items/store/seq_no/{seq_no}/song/{song_id}/{moreItems?}/{beforeItem?}',     'Cspot\ItemController@insertSong');    
    // update item with new song_id 
    Route::get('plans/{plan_id}/items/update/item/{item_id}/song/{song_id}',  'Cspot\ItemController@updateSong');    
    // show form to create a new item for a plan
    Route::get('plans/{plan_id}/items/create/{seq_no}',                        'Cspot\ItemController@create');    
    // show form to update a new item for a plan
    Route::get('plans/{plan_id}/items/{item_id}/edit', ['as'=>'cspot.items.edit', 'uses'=>'Cspot\ItemController@edit']);    
    // MOVE the specified resource up or down in the list of items related to a plan
    Route::get('items/{item_id}/move/{direction}',          'Cspot\ItemController@move');
    // change the seq no of an item
    Route::get('items/{item_id}/seq_no/{seq_no}',           'Cspot\ItemController@update');

    // soft delete: specific route using 'get' method
    Route::get('items/{item_id}/delete',                    'Cspot\ItemController@trash');    
    // permanently delete an item
    Route::get('items/{item_id}/permDelete',                'Cspot\ItemController@permDelete');    
    // restore a soft-deleted item
    Route::get('items/{item_id}/restore',                   'Cspot\ItemController@restore');    
    // delete all trashed items of a plan
    Route::get('plans/{plan_id}/items/trashed/restore',     'Cspot\ItemController@restoreAllTrashed');    
    // delete all trashed items of a plan
    Route::get('plans/{plan_id}/items/trashed/delete',      'Cspot\ItemController@deleteAllTrashed');    

    // presentation view of a plan
    Route::get('items/{item_id}/{present?}',                'Cspot\ItemController@show');


    // API: update item data using AJAX
    // this route gets the item id via form field
    Route::post('api/items/update',           ['as'=>'cspot.api.item.update',   'uses'=>'Cspot\ItemController@APIupdate']);
    // insert new item
    Route::post('api/items',                  ['as'=>'cspot.api.item',          'uses'=>'Cspot\ItemController@APIinsert']);
    // item id via URL
    Route::post('api/items/{item_id}/update', ['as'=>'cspot.api.items.update',  'uses'=>'Cspot\ItemController@APIupdate']);
    Route::post('api/items/{item_id}/delete', ['as'=>'cspot.api.items.delete',  'uses'=>'Cspot\ItemController@APIdelete']);


    // unlink a song from an item
    Route::put('items/{item_id}/unlinkSong/{song_id}',                                  'Cspot\ItemController@unlinkSong');

    // Item NOTES (add, update, delete controlled via )
    Route::get( 'api/items/{item_id}/note',                                             'Cspot\ItemController@APIgetItemNotes');
    Route::post('api/items/{item_id}/note',   ['as'=>'cspot.api.items.note',    'uses'=>'Cspot\ItemController@APIitemNotes']);



    /**
     * FILES
     */
    // list all current files
    Route::get('files/',     ['as'=> 'cspot.files', 'uses'=>'Cspot\FileController@index']);
    // updata file information
    Route::post('files/{id}',                               'Cspot\FileController@update');
    // add a file to a plan item
    Route::get('items/{item_id}/addfile/{file_id}',         'Cspot\FileController@add');    
    // change seq_no of a file
    Route::get('items/{item_id}/movefile/{file_id}/{dir}',  'Cspot\FileController@move');    
    // unlink an attachment from an item
    Route::put('items/{item_id}/unlink/{file_id}',          'Cspot\FileController@unlink');

    // delete an attachment to a song or an item
    Route::delete('files/{id}/delete',                      'Cspot\FileController@delete');


    /*
        SONGS
    */
        
    Route::get('songs/search',             'Cspot\SongController@searchSong');
    // basic songs processing
    Route::resource('songs',               'Cspot\SongController');
        
    // song search
    Route::post('songs/search',            'Cspot\SongController@searchSong');

    // specific delete route using 'get' method
    Route::get('songs/{songs}/delete',     'Cspot\SongController@destroy');

    // SPA API
    Route::post('api/songs/update',           [ 'uses'=>'Cspot\SongController@APIupdate']);

    // song list
    Route::get('api/songs/getsonglist', ['as'=>'getsonglist', 'uses'=>'Cspot\SongController@APIgetSongList']);


    /*
        SYNC PRESENTATION
    */
    // Manage Main Presenter
    Route::put('presentation/mainPresenter', ['as'=>'presentation.mainPresenter.set', 'uses'=>'Cspot\PresentationController@setMainPresenter']);
    // Send current show position
    Route::put('presentation/setPositon',    ['as'=>'presentation.position.set',      'uses'=>'Cspot\PresentationController@setPosition']);
    // define sync stream
    Route::get('presentation/sync',          ['as'=>'presentation.sync',              'uses'=>'Cspot\PresentationController@syncPresentation']);

});



/*
|--------------------------------------------------------------------------
| Routes for messages between users
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'messages', 'middleware' => ['web', 'auth']], function () {
    Route::get('/',       ['as' => 'messages',        'uses' => 'MessagesController@index' ]);
    Route::get('create',  ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
    Route::post('/',      ['as' => 'messages.store',  'uses' => 'MessagesController@store' ]);
    Route::get('{id}',    ['as' => 'messages.show',   'uses' => 'MessagesController@show'  ]);
    Route::put('{id}',    ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
    Route::get('{id}/delete', ['as' => 'messages.delete', 'uses' => 'MessagesController@delete']);
});




/*
|--------------------------------------------------------------------------
| Routes for users with special rights
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => ['web']], function() {

    // admin only: CRUD for users
    Route::resource('users',    'Admin\UserController');    
    Route::resource('roles',     'Admin\RoleController');    
    Route::resource('resources',  'Admin\ResourceController');    
    Route::resource('instruments', 'Admin\InstrumentController');    
    Route::resource('types',        'Admin\TypeController');    
    Route::resource('default_items', 'Admin\DefaultItemController');    
    Route::resource('file_categories','Admin\FileCategoryController');    
    // as forms cannot use DELETE method, we implement it as GET
    Route::get('users/{users}/delete',    'Admin\UserController@destroy');    
    Route::get('roles/{roles}/delete',       'Admin\RoleController@destroy');    
    Route::get('resources/{resources}/delete',  'Admin\ResourceController@destroy');    
    Route::get('instruments/{instrument}/delete',  'Admin\InstrumentController@destroy');    
    Route::get('types/{types}/delete',               'Admin\TypeController@destroy');    
    Route::get('default_items/{default_items}/delete',  'Admin\DefaultItemController@destroy');    

    // user wants to set current page as their homepage
    Route::post('users/{user_id}/setstartpage', ['as'=>'user.setstartpage', 'uses' => 'Admin\UserController@setStartPage']);

    // run a specific job
    Route::get('runjob/batch', function() {
        dispatch(new App\Jobs\BatchJobs);
        flash( 'Done!');
        return redirect()->back();
    });

});

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::get('admin/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('admin/customize', 'Admin\CustomizeController@index');
    Route::post('admin/customize', 'Admin\CustomizeController@update');
});
