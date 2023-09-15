<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/register", [AuthController::class, 'register']);
Route::post("/login", [AuthController::class, 'login']);
Route::get("/product", [ProductController::class, 'getAllProduct']);
Route::post("/product/insert", [ProductController::class, 'insert']);
Route::post("/product/update", [ProductController::class, 'update']);
Route::post("/product/delete", [ProductController::class, 'delete']);
Route::get("/product/filter", [ProductController::class, "filter"]);
