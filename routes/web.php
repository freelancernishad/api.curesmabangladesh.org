<?php

use App\Models\Article;
use App\Services\DateService;
use App\Services\ContentService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InvoiceController;

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
    return ['Laravel' => app()->version()];
});




require __DIR__.'/auth.php';



Route::get('/invoice/{name}/{id}', [InvoiceController::class,'invoice']);


Route::get('/files/{path}', function ($path) {

    // Serve the file from the protected disk
    return response()->file(Storage::disk('protected')->path($path));
})->where('path', '.*');


Route::get('/protected/image/{path}', [ImageController::class, 'show'])->where('path', '.*')->name('protected.image');
