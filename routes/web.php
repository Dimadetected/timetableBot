<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Auth;

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

Route::prefix('admin')->name('admin.')->group(function () {
    
    //Расписание
    Route::get('/', 'TimetableController@index')->name('index');
    Route::prefix('timetable')->name('timetable.')->group(function () {
        Route::get('/', 'TimetableController@index')->name('index');
        Route::get('/form/{id?}', 'TimetableController@form')->name('form');
        Route::post('/store/', 'TimetableController@store')->name('store');
    });
    
    //Шаблоны
    Route::prefix('templates')->name('templates.')->group(function () {
        Route::get('/', 'TemplateController@index')->name('index');
        Route::get('/form/{id?}', 'TemplateController@form')->name('form');
        Route::post('/store/', 'TemplateController@store')->name('store');
    });
    
    //Пары
    Route::prefix('lectures')->name('lectures.')->group(function () {
        Route::get('/', 'LectureController@index')->name('index');
        Route::get('/form/{id?}', 'LectureController@form')->name('form');
        Route::post('/store/', 'LectureController@store')->name('store');
    });
    
    //Курсы
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', 'CourseController@index')->name('index');
        Route::get('/form/{id?}', 'CourseController@form')->name('form');
        Route::post('/store/', 'CourseController@store')->name('store');
    });
    
    //Факультеты
    Route::prefix('faculties')->name('faculties.')->group(function () {
        Route::get('/', 'FacultyController@index')->name('index');
        Route::get('/form/{id?}', 'FacultyController@form')->name('form');
        Route::post('/store/', 'FacultyController@store')->name('store');
    });
    
    //Группы
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', 'GroupController@index')->name('index');
        Route::get('/form/{id?}', 'GroupController@form')->name('form');
        Route::post('/store/', 'GroupController@store')->name('store');
    });
    //Типы Юзеров
    Route::prefix('usersType')->name('usersType.')->group(function () {
        Route::get('/', 'UsersTypeController@index')->name('index');
        Route::get('/form/{id?}', 'UsersTypeController@form')->name('form');
        Route::post('/store/', 'UsersTypeController@store')->name('store');
    });
});
//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
