<?php

Route::get('/import_pages', 'PageController@importPages');
Route::get('/import_posts', 'PageController@importPosts');
Route::get('/import_engagements/{time}', 'PageController@importEngagements');
Route::get('/view_links', 'PageController@viewTopLinks');


Route::get('/import_accounts', 'AccountController@importAccounts');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

/**
 * Backend routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'auth'], function () {

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');

    //Accounts

    Route::get('accounts', 'AccountController@index')->name('accounts');
    Route::get('accounts/open_profile/{profile?}', 'AccountController@openProfile')->name('accounts.profile');
    Route::get('accounts/list', 'AccountController@all')->name('accounts.list');
    Route::get('accounts/{account?}', 'AccountController@show')->name('accounts.show');
    Route::get('accounts/{account}/change_password', 'AccountController@changePassword')->name('accounts.change_password');
    Route::get('accounts/{account}/change_email_password', 'AccountController@changeEmailPassword')->name('accounts.change_email_password');
    Route::get('accounts/{account}/generate_token', 'AccountController@generateToken')->name('accounts.generate_token');
    Route::get('accounts/{account}/update_info', 'AccountController@updateInfo')->name('accounts.update_info');
    Route::get('accounts/{account}/backup_friends', 'AccountController@backupFriends')->name('accounts.backup_friends');
    Route::get('accounts/{account}/view_friends', 'AccountController@viewFriends')->name('accounts.view_friends');
    Route::post('accounts/{account}', 'AccountController@update')->name('accounts.update');

    //Browsers
    Route::get('browsers', 'BrowserController@index')->name('browsers');
    Route::get('browsers/list', 'BrowserController@all')->name('browsers.list');
    Route::get('browsers/import', 'BrowserController@import')->name('browsers.import');

    //My Pages
    Route::get('my_pages/test', 'MyPageController@test')->name('my_pages.test');
    Route::get('my_pages/create', 'MyPageController@create')->name('my_pages.create');
    Route::post('my_pages/{page}/save', 'MyPageController@save')->name('my_pages.save');
    Route::get('my_pages/{page}/update_info', 'MyPageController@updateInfo')->name('my_pages.update_info');
    Route::get('my_pages', 'MyPageController@index')->name('my_pages');
    Route::get('my_pages/list', 'MyPageController@all')->name('my_pages.list');
    Route::get('my_pages/{page?}', 'MyPageController@show')->name('my_pages.show');

    //Group
    Route::get('groups/create', 'GroupController@create')->name('groups.create');
    Route::post('groups/{group}/save', 'GroupController@save')->name('groups.save');
    Route::get('groups', 'GroupController@index')->name('groups');
    Route::get('groups/list', 'GroupController@all')->name('groups.list');
    Route::get('groups/{group}', 'GroupController@show')->name('groups.show');

    //Site
    Route::get('sites/create', 'SiteController@create')->name('sites.create');
    Route::post('sites/{site}/save', 'SiteController@save')->name('sites.save');
    Route::get('sites', 'SiteController@index')->name('sites');
    Route::get('sites/list', 'SiteController@all')->name('sites.list');
    Route::get('sites/{site}', 'SiteController@show')->name('sites.show');

    //Links
    Route::get('links', 'LinkController@index')->name('links');
    Route::get('links/list', 'LinkController@all')->name('links.list');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
