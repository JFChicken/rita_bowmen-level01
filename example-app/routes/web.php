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

Route::get('/', function () {


    foreach (Storage::allFiles('public/') as $file){
        $fileurl = explode('.',$file);

        if(in_array('png',$fileurl)){
            $finalList[] = $file;
        }
    }

    return view('demo',['Imagelist'=>$finalList]);
});
