<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

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
    return view('welcome', ['products' => ['apple', 'bread', 'butter', 'cheese', 'corn', 'dill', 'eggs', 
    'ice_cream', 'kidney_bean', 'milk', 'nutmeg', 'onion', 'sugar', 'unicorn', 'yogurt', 'chocolate'],
     'recommendation' => null, 'error' => null]);
});

Route::get('setup', [TransactionController::class, 'make_apriori']);
Route::get('import', [TransactionController::class, 'import']);
Route::get('recommend', [TransactionController::class, 'welcome']);
