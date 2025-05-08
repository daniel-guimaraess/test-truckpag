<?php

namespace App\Services;

use App\Models\ImportData;
use App\Repositories\ImportDataRepository;

class ImportDataService
{
    protected $importDataRepository;

    public function __construct(ImportDataRepository $importDataRepository)
    {
        $this->importDataRepository = $importDataRepository;
    }

    public function importDataOpenFoodFacts(){
        
        try {
            $lastImport = ImportData::latest()->first();
            
            if($lastImport){
                if ($lastImport->status === 'fail') {
                
                    $lastFileNumber = $lastImport->last_file_number;

                } else {
                    $lastFileNumber = $lastImport->last_file_number + 1;
                }
            }
            else{
                $lastFileNumber = 1;
            }            

            $lastFileNumberFormatted = str_pad($lastFileNumber, 2, '0', STR_PAD_LEFT);

            $lastFile = 'products_'.$lastFileNumberFormatted.'.json.gz' ?: 'products_01.json.gz';

            $url = 'https://challenges.coode.sh/food/data/json/'.$lastFile;
            $localPath = storage_path('app/'.$lastFile);
            $fileContent = file_get_contents($url);

            if ($fileContent) {

                file_put_contents($localPath, $fileContent);

                if (file_exists($localPath)) {
                    $gz = gzopen($localPath, 'r');
                    $produtos = [];
                    $count = 0;

                    while (!gzeof($gz) && $count < 100) {
                        $linha = gzgets($gz);
                        $item = json_decode($linha, true);

                        if (is_array($item)) {
                            $produtos[] = $item;
                            $count++;
                        }
                    }

                    gzclose($gz);
                    unlink($localPath);

                    $this->importDataRepository->create($produtos);

                    ImportData::updateOrCreate(
                        ['last_file_number' => $lastFileNumber],
                        ['status' => 'success']
                    );
                }
            }

        } catch (\Exception $e) {
           
            $lastImport = ImportData::latest()->first();
            
            if ($lastImport) {
                $lastFileNumber = $lastImport->last_file_number + 1;
            } else {
                $lastFileNumber = 1;
            }

            ImportData::updateOrCreate(
                ['last_file_number' => $lastFileNumber],
                ['status' => 'fail']
            );
        }
    }

    public function checkLastImportDataOpenFoodFacts(){

        $lastImport = ImportData::latest()->first();

        if($lastImport->status === 'fail'){
            
            $this->importDataOpenFoodFacts();
        }
    }
}
