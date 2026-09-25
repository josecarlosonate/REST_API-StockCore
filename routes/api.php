<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductSupplierController;
use App\Http\Controllers\Api\V1\StockMovementController;
use App\Http\Controllers\Api\V1\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Ruta Login - numero de registro asignado 320916
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function () {

        // Rutas de productos
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        // Rutas de Categorias
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::get('/categories/{category}/products', [CategoryController::class, 'products']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::patch('/categories/{category}', [CategoryController::class, 'update']);
        // Ruta logout
        Route::post('/logout', [AuthController::class, 'logout']);
        // Rutas de provedores de productos
        Route::get('/suppliers', [SupplierController::class, 'index']);
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);
        Route::post('/suppliers', [SupplierController::class, 'store']);
        Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update']);
        // Rutas relaciones producto-proveedor
        Route::post('/products/{product}/suppliers', [ProductSupplierController::class, 'store']);
        Route::patch('/products/{product}/suppliers/{supplier}', [ProductSupplierController::class, 'update']);
        Route::delete('/products/{product}/suppliers/{supplier}', [ProductSupplierController::class, 'destroy']);
        Route::get('/products/{product}/suppliers', [ProductSupplierController::class, 'index']);
        // Rutas de inventario
        Route::get('/inventories', [InventoryController::class, 'index']);
        Route::get('/products/{product}/inventory', [InventoryController::class, 'show']);
        Route::patch('/products/{product}/inventory', [InventoryController::class, 'update']);
        // Ruta movimientos de stock
        Route::post('/products/{product}/stock-movements', [StockMovementController::class, 'store']);
        Route::get('/products/{product}/stock-movements', [StockMovementController::class, 'index']);
        Route::get('/stock-movements', [StockMovementController::class, 'listMovements']);
        // Ruta de clientes
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::get('/customers/{customer}', [CustomerController::class, 'show']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::patch('/customers/{customer}', [CustomerController::class, 'update']);
        // Ruta de ordenes de venta
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
    });
});
