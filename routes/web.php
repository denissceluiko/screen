<?php

use App\Http\Controllers\ScreenController;
use App\Livewire\ShowScreen;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('filament.app.auth.login');
});

Route::get('/s/{screen:slug}', ShowScreen::class)->name('screen.display');
