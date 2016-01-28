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


});



/*
|--------------------------------------------------------------------------
|    Routes for the core application
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'cspot', 'middleware' => ['web', 'auth']], function() {

    // show only upcoming service plans
    Route::get('plans/future', ['as'=>'future', 'uses'=>'Cspot\PlanController@future']);
    // basic CRUD resources for plans
    Route::resource('plans', 'Cspot\PlanController');
    // allow DELETE via the GET method
    Route::get('plans/{plans}/delete', 'Cspot\PlanController@destroy');    
    // show filtered resources (only future by default!)
    Route::get('plans/by_user/{user_id}/{all?}', 'Cspot\PlanController@by_user');    
    Route::get('plans/by_type/{type_id}/{all?}', 'Cspot\PlanController@by_type');    
    // update (append) the note for a plan
    Route::put('plans/{plans}/addNote', ['as'=>'addNote', 'uses'=>'Cspot\PlanController@addNote']);
    
    // route to show form to create a new item for a plan
    Route::get('plans/{plan_id}/items/create/{seq_no}', 'Cspot\ItemController@create');    
    // route to show form to update a new item for a plan
    Route::get('plans/{plan_id}/items/{item_id}/edit', 'Cspot\ItemController@edit');    

    // generic item resource routes
    Route::resource('items', 'Cspot\ItemController');
    // specific delete route using 'get' method
    Route::get('items/{items}/delete', 'Cspot\ItemController@destroy');    

    // basic songs processing
    Route::resource('songs', 'Cspot\SongController');

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
