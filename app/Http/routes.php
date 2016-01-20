<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

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


// Routes for users with special rights
Route::group(['prefix' => 'admin', 'middleware' => ['web']], function() {

    // admin only: CRUD for users
    Route::resource('users', 'Admin\UserController');    
    Route::resource('roles', 'Admin\RoleController');    
    // as forms cannot use DELETE method, we implement it as GET
    Route::get('users/{users}/delete', 'Admin\UserController@destroy');    
    Route::get('roles/{roles}/delete', 'Admin\RoleController@destroy');    

});
