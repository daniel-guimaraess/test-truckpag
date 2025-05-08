<?php

use App\Services\ImportDataService;
use Illuminate\Support\Facades\Artisan;

Artisan::command('import-data-food-open-facts', function () {

    $importDataService = app(ImportDataService::class);

    $importDataService->importDataOpenFoodFacts();

})->purpose('Import data from Food Open Facts at 02:00 AM')->dailyAt('02:00');


Artisan::command('check-last-import-data-food-open-facts', function () {

    $importDataService = app(ImportDataService::class);

    $importDataService->checkLastImportDataOpenFoodFacts();

})->purpose('Check last import data from Food Open Facts')->hourly();