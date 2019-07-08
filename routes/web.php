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


Route::get('/getConsultores','CoUsuarioController@getConsultores')->name('getConsutores');

Route::get('/getFacturas','ConDesempenhoController@getDesempenhoCosultor')->name('getFacturas');
Route::post('/relatorio','ConDesempenhoController@relatorio')->name('relatorio');
Route::post('/grafico','ConDesempenhoController@grafico')->name('grafico');
Route::post('/percentageReceitaLiquida','ConDesempenhoController@percentageReceitaLiquida')->name('percentageReceitaLiquida');

Route::get('/','ConDesempenhoController@index');


