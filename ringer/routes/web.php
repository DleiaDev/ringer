<?php

// Auth
Auth::routes();

// Main
Route::get('/', 'PagesController@app')->name('home')->middleware('auth');

/* API */
// Contacts
Route::get('/api/contacts/{id}', 'ContactsController@getContact');
Route::get('/api/contacts', 'ContactsController@getContacts');
Route::post('/api/contacts/add', 'ContactsController@addContact');
// Messages
Route::post('/api/send', 'ContactsController@sendMessage');
Route::post('/api/seen', 'ContactsController@readMessage');
Route::post('/api/receive', 'ContactsController@receiveMessage');
Route::get('/api/contacts/{id}/messages', 'ContactsController@getMessages');
// User
Route::post('/api/user', 'UsersController@updateUser');
Route::post('/api/user/photo', 'UsersController@updatePhoto');
Route::put('/api/user/password', 'UsersController@changePassword');
