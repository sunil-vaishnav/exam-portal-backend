<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FormController;
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('forms', [FormController::class,'index']);
Route::get('forms/{form}', [FormController::class,'show']);

Route::middleware('jwt.auth')->group(function(){

    Route::post('submissions', [SubmissionController::class,'store']);
    Route::get('submissions', [SubmissionController::class,'index']); 
    Route::get('submissions/{id}', [SubmissionController::class,'show']);

    Route::post('payments/initiate', [PaymentController::class,'initiate']);
    Route::post('payments/verify', [PaymentController::class,'verify']);
});

// admin-only
Route::middleware(['jwt.auth','role:admin'])->group(function(){
    Route::post('forms/create', [FormController::class,'create']);
    Route::put('forms/update/{form}', [FormController::class,'update']);
    Route::delete('forms/delete/{form}', [FormController::class,'destroy']);
    Route::get('admin/submissions', [SubmissionController::class,'adminIndex']);
    Route::get('admin/payments', [PaymentController::class,'adminIndex']);
});
