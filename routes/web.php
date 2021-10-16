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
    if(Auth::check()){
         return view('home',[App\Http\Controllers\DefaultController::class, 'index']);
    }else{
        return redirect('/login');
    }
});

Route::middleware(['auth'])->prefix('git')->group(function(){
    Route::get('/fetch_user_data',[App\Http\Controllers\DefaultController::class,'fetchUserData'])->name('fetch_user_data');
});


Auth::routes();
