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

Auth::routes([
	'register' => false,
	'reset' => false,
	'verify' => false,
	]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/attendance', 'AttendanceController@index')->name('attendance');

Route::resource('holidays', 'HolidayController');
Route::get('holidays/{holiday}/approve', 'HolidayController@approve')->name('holidays.approve');
Route::get('holidays/{holiday}/deny', 'HolidayController@deny')->name('holidays.deny');