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
    'reset'    => false,
    'verify'   => false,
]);

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/home', 'HomeController@store')->name('home.store');
Route::get('/attendance', 'AttendanceController@index')->name('attendance');
Route::get('/audit', 'AuditController@index')->name('audit');
Route::get('/log', 'DownloadController@index')->name('log');
Route::post('/log', 'DownloadController@select')->name('log.select');

Route::resource('absences', 'AbsenceController');
Route::resource('absencetypes', 'AbsenceTypeController');
Route::resource('config', 'ConfigController');
Route::resource('doorevents', 'DoorEventController');
Route::resource('downloads', 'DownloadGroupController');
Route::resource('fobs', 'FobController');
Route::resource('holidays', 'HolidayController');
Route::get('holidays/{secret}/approve', 'HolidayController@approve')->name('holidays.approve'); // Do these need to be here?
Route::get('holidays/{secret}/deny', 'HolidayController@deny')->name('holidays.deny');          // Can't the resource controller "just do this"?
Route::resource('staff', 'EmployeeController');
Route::resource('telephones', 'TelephoneController');
Route::resource('workstates', 'WorkStateController');
