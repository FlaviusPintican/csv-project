<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvController;

Route::get('/csv/statistics', [CsvController::class, 'getStatistics'])->name('csv.statistics');
