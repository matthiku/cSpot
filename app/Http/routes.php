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
    Route::get('/home', 'HomeController@index')->name('home');

    // all authorization routes
    //Route::auth();  (old 5.2 way)
    Auth::routes();

    // allow get-method for logout again
    Route::get('/logout', 'Auth\LoginController@logout');

    // confirm registration route from registration email
    Route::get('register/confirm/{token}',    'Auth\RegisterController@confirmEmail');


    // Social (OAuth) authorization
    $s = '';
    Route::get('/social/redirect/{provider}', 'Auth\RegisterController@getSocialRedirect')->name('social.redirect');
    Route::get('/social/handle/{provider}',   'Auth\RegisterController@getSocialHandle'  )->name('social.handle');


    // API route to compile bible references
    Route::get('bible/books',                           'Cspot\BibleController@books');         // get all books
    Route::get('bible/books/all/chapters',              'Cspot\BibleController@allChapters');   // get chapter numbers of ALL books
    Route::get('bible/books/all/verses',                'Cspot\BibleController@allVerses')->name('bible.books.all.verses'); // get chapter and verse numbers of ALL books
    Route::get('bible/books/{book}',                    'Cspot\BibleController@chapters');      // get chapter numbers of a book
    Route::get('bible/books/{book}/chapters/{chapter}', 'Cspot\BibleController@verses');        // get verse numbers of a chapter
    // get bible texts
    Route::get('bible/text/{version}/{book}/{chapter}/','Cspot\BibleController@getChapter')->name('bible.chapter');     // get bible text
    Route::get('bible/passage/{version}/{book}/{chapter}/{verseFrom}/{verseTo}/','Cspot\BibleController@getBibleText'); // get bible passage

    // user confirms his partizipation with TOKEN (no login needed!)
    Route::get( 'cspot/plans/{plan_id}/team/{team_id}/confirm/{token}',                'Cspot\TeamController@confirm');

    // get next event of any type
    Route::get('api/plans/next',                   'Cspot\PlanController@APInextEvent')->name('api.next.event');

});





/*
|--------------------------------------------------------------------------
|    Routes for the core application
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'cspot', 'middleware' => ['web', 'auth']], function() {


    // get the base config data for cSpot to the front-end
    Route::get('api/config/get',                    'HomeController@APIconfigGet')->name('APIconfigGet');


    /*
         PLANS
    */

    // show next Sunday's Service plan
    Route::get('plans/next',                       'Cspot\PlanController@nextSunday'  )->name('next');

    // basic CRUD resources for plans, but without the simple GET method
    Route::resource('plans',                       'Cspot\PlanController');

    // show one plan for the selected date
    Route::get('plans/by_date/{date}',             'Cspot\PlanController@by_date');    

    // allow DELETE via the GET method
    Route::get('plans/delete/{plan_id}',           'Cspot\PlanController@destroy');    

    // send an email reminder to a leader or teacher for a plan
    Route::get('plans/{plan_id}/remind/{user_id}', 'Cspot\PlanController@sendReminder')->name('sendReminder');

    // store client-rendered presentation slides locally for other users to download
    Route::post( 'plan/{plan_id}/cache',           'Cspot\PlanController@postCache');
    Route::get(  'plan/{plan_id}/cache',           'Cspot\PlanController@getCache');
    Route::post( 'plan/{plan_id}/cache/delete',    'Cspot\PlanController@deleteCache');

    // API get plan    
    Route::post('api/plan/get',                    'Cspot\PlanController@APIgetPlan')->name('api.plan.get');
    // API update    
    Route::post('api/plan/update',                 'Cspot\PlanController@APIupdate' )->name('api.plan.update');
    // update (append) the note for a plan
    Route::post('plans/addNote',                   'Cspot\PlanController@APIaddNote')->name('api.addNote');


    /*
        PLAN TEAMS
    */
    // Manage team for a service plan
    Route::get( 'plans/{plan_id}/team',                     'Cspot\TeamController@index')->name('team.index');
    Route::post('plans/{plan_id}/team',                     'Cspot\TeamController@store')->name('team.store');
    // user announces his availability for a certain plan
    Route::get( 'plans/{plan_id}/team/available/{bool}',    'Cspot\TeamController@available');
    // shortcut to add all musicians to a plan
    Route::get( 'plans/{plan_id}/team/addAllMusicians',     'Cspot\TeamController@addAllMusicians');
    // show the form to edit an existing team member
    Route::get( 'plans/{plan_id}/team/{team_id}/edit',      'Cspot\TeamController@edit');
    // update an existing team member
    Route::post('plans/{plan_id}/team/{team_id}/update',    'Cspot\TeamController@update');
    // delete an existing team member
    Route::get( 'plans/{plan_id}/team/{team_id}/delete',    'Cspot\TeamController@destroy');
    // send email to user to request his partizipation
    Route::get( 'plans/{plan_id}/team/{team_id}/sendrequest', 'Cspot\TeamController@sendrequest');
    // user confirms his partizipation
    Route::get( 'plans/{plan_id}/team/{team_id}/confirm',   'Cspot\TeamController@confirm');


    /*
        RESOURCES
    */
    // Manage resources for a service plan
    Route::get( 'plans/{plan_id}/resource',                     'Cspot\ResourceController@index')->name('resource.index');
    Route::post('plans/{plan_id}/resource',                     'Cspot\ResourceController@store')->name('resource.store');
    // show the form to edit an existing resource
    //Route::get( 'plans/{plan_id}/resource/{resource_id}/edit','Cspot\ResourceController@edit');
    // update an existing resource
    Route::post('api/plans/resource/update',                    'Cspot\ResourceController@APIupdate');
    // delete an existing resource
    Route::get( 'plans/{plan_id}/resource/{resource_id}/delete','Cspot\ResourceController@destroy');


    Route::get('history',                                       'Cspot\HistoryController@index');


    /*
         ITEMS
     */

    // update a specific item (this is usually called from a form)
    Route::put('items/{item_id}',                                           'Cspot\ItemController@update')->name('cspot.items.update');
    Route::post('items',                                                    'Cspot\ItemController@store')->name('cspot.items.store');
    // add song directly from the song list to a plan
    Route::get('plans/{plan_id}/addsong/{song_id}',                         'Cspot\ItemController@addSong');
    // show form of next or previous item for a plan
    Route::get('plans/{plan_id}/items/{item_id}/go/{direction}/{chords?}',  'Cspot\ItemController@next');
    // show form to create a new item for a plan
    Route::get('plans/{plan_id}/items/create/before/{item_id}',             'Cspot\ItemController@create');
    // insert new item with song_id 
    Route::get('plans/{plan_id}/items/store/seq_no/{seq_no}/song/{song_id}/{moreItems?}/{beforeItem?}',     'Cspot\ItemController@insertSong');    
    // update item with new song_id 
    Route::get('plans/{plan_id}/items/update/item/{item_id}/song/{song_id}', 'Cspot\ItemController@updateSong');
    // show form to create a new item for a plan
    Route::get('plans/{plan_id}/items/create/{seq_no}',                      'Cspot\ItemController@create');
    // show form to update a new item for a plan
    Route::get('plans/{plan_id}/items/{item_id}/edit',      'Cspot\ItemController@edit')->name('cspot.items.edit');
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
    Route::get('items/{item_id}/{present?}',                'Cspot\ItemController@show')->name('cspot.items.present');


    // API: update item data using AJAX
    // this route gets the item id via form field
    Route::post('api/items/update',                         'Cspot\ItemController@APIupdate')->name('cspot.api.item.update');
    // insert new item
    Route::post('api/items',                                'Cspot\ItemController@APIinsert')->name('cspot.api.item');
    // item id via URL
    Route::post('api/items/{item_id}/update',               'Cspot\ItemController@APIupdate')->name('cspot.api.items.update');
    Route::post('api/items/{item_id}/delete',               'Cspot\ItemController@APIdelete')->name('cspot.api.items.delete');


    // unlink a song from an item
    Route::put('items/{item_id}/unlinkSong/{song_id}',      'Cspot\ItemController@unlinkSong');

    // Item NOTES (add, update, delete controlled via )
    Route::get( 'api/items/{item_id}/note',                 'Cspot\ItemController@APIgetItemNotes');
    Route::post('api/items/{item_id}/note',                 'Cspot\ItemController@APIitemNotes')->name('cspot.api.items.note');




    /**
     * FILES
     */
    // list all current files
    Route::get('files/',                                    'Cspot\FileController@index')->name('cspot.files');
    // updata file information
    Route::post('files/{id}',                               'Cspot\FileController@update');
    // add a file to a plan item
    Route::get('items/{item_id}/addfile/{file_id}',         'Cspot\FileController@add');    
    // change seq_no of a file
    Route::get('items/{item_id}/movefile/{file_id}/{dir}',  'Cspot\FileController@move');    
    // unlink an attachment from an item
    Route::put('api/items/file/unlink/',                    'Cspot\FileController@APIunlink')->name('api.items.file.unlink');

    // delete an attachment to a song or an item
    Route::delete('files/{id}/delete',                      'Cspot\FileController@delete');

    // API

    // get files by category or all
    Route::get('api/files/{category?}', 'Cspot\FileController@APIindex')->name('cspot.api.files');
    // add a file to a plan item
    Route::post('api/items/addfile',    'Cspot\FileController@add'    )->name('cspot.api.addfile');    
    // add a file 
    Route::post('api/files/upload',     'Cspot\FileController@upload')->name('cspot.api.upload');    




    /*
        SONGS
    */
    Route::get('songs/search',          'Cspot\SongController@searchSong');

    // basic songs processing
    Route::resource('songs',            'Cspot\SongController');
        
    // song search
    Route::post('songs/search',         'Cspot\SongController@searchSong');

    // specific delete route using 'get' method
    Route::get('songs/{songs}/delete',  'Cspot\SongController@destroy');

    // SONGS API

    Route::post('api/songs/update',     'Cspot\SongController@APIupdate');
    // Unlink file
    Route::put('api/songs/file/unlink', 'Cspot\SongController@APIunlink')->name('api.songs.file.unlink');

    // song list
    Route::get('api/songs/getsonglist', 'Cspot\SongController@APIgetSongList')->name('getsonglist');


    // list of training videos
    Route::get('training/videos',       'Cspot\SongController@trainingVideos')->name('trainingVideos');


    /*
        SYNC PRESENTATION
    */
    // Manage Main Presenter
    Route::put('presentation/mainPresenter', 'Cspot\PresentationController@setMainPresenter')->name('presentation.mainPresenter.set');
    // Send current show position
    Route::put('presentation/setPositon',    'Cspot\PresentationController@setPosition'     )->name('presentation.position.set');
    // define sync stream
    Route::get('presentation/sync',          'Cspot\PresentationController@syncPresentation')->name('presentation.sync');

});



/*
|--------------------------------------------------------------------------
| Routes for messages between users
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'messages', 'middleware' => ['web', 'auth']], function () {
    Route::get('/',           'MessagesController@index' )->name('messages');
    Route::get('create',      'MessagesController@create')->name('messages.create');
    Route::post('/',          'MessagesController@store' )->name('messages.store');
    Route::get('{id}',        'MessagesController@show'  )->name('messages.show');
    Route::put('{id}',        'MessagesController@update')->name('messages.update');
    Route::get('{id}/delete', 'MessagesController@delete')->name('messages.delete');
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
    Route::get('users/{id}/delete',    'Admin\UserController@destroy');    
    Route::get('roles/{id}/delete',     'Admin\RoleController@destroy');    
    Route::get('resources/{id}/delete',  'Admin\ResourceController@destroy');    
    Route::get('instruments/{id}/delete', 'Admin\InstrumentController@destroy');    
    Route::get('types/{id}/delete',        'Admin\TypeController@destroy');    
    Route::get('default_items/{id}/delete', 'Admin\DefaultItemController@destroy');    
    Route::get('file_categories/{id}/delete','Admin\FileCategoryController@destroy');    

    // user wants to set current page as their homepage
    Route::post('users/{user_id}/setstartpage',         'Admin\UserController@setStartPage')->name('user.setstartpage');

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
