<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'HomeController@index')->name('home');
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('auth/google', 'Auth\GoogleController@redirectToGoogle');
Route::get('auth/google/callback', 'Auth\GoogleController@handleGoogleCallback');

Route::post('currency-tableAjax','HomeController@currencytableAjax')->name('currency-tableAjax');
Route::post('create-currency','HomeController@store')->name('create-currency');
Route::post('update-currency','HomeController@update')->name('update-currency');
Route::get('delete-currency/{id}','HomeController@delete')->name('delete-currency');
Route::get('currency-compare','HomeController@compare_currency')->name('currency-compare');
Route::get('currency-compare/{id}','HomeController@compare_with_base_currency');
Route::post('append_more','HomeController@append_more')->name('append_more');
Route::post('ajax_comparison','HomeController@ajax_comparison')->name('ajax_comparison');

