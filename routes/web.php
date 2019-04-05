<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import_pages', 'PageController@importPages');
Route::get('/import_posts', 'PageController@importPosts');
Route::get('/import_engagements/{time}', 'PageController@importEngagements');
Route::get('/view_links', 'PageController@viewTopLinks');


Route::get('/import_accounts', 'AccountController@importAccounts');
Route::get('/generate_token/{id}', 'AccountController@generateToken');
Route::get('/update_account/{id}', 'AccountController@updateAccount');