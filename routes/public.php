<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvController;

Route::post('/csv/statistics', [CsvController::class, 'getStatistics']);
