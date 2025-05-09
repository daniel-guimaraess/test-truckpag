<?php

namespace App\Services;

use App\Models\ImportData;
use App\Repositories\ImportDataRepository;
use Illuminate\Support\Facades\Log;

class ImportDataService
{
    protected $importDataRepository;
    protected $alertService;

    public function __construct(ImportDataRepository $importDataRepository, AlertService $alertService)
    {   
        $this->importDataRepository = $importDataRepository;
        $this->alertService = $alertService;
    }

    public function importDataOpenFoodFacts(){
        
        try {
            Log::channel('importdata')->info('------------------------------------------------------');
            Log::channel('importdata')->info('Inicializando Cron para importar dados');

            $lastImport = ImportData::latest()->first();
            
            if($lastImport){
                if ($lastImport->status === 'fail') {
                    
                    $lastFileNumber = $lastImport->last_file_number;

                } else {
                    $lastFileNumber = $lastImport->last_file_number + 1;
                }
            }
            else{
                Log::channel('importdata')->info('Primeiro import');
                $lastFileNumber = 1;
            }            

            $lastFileNumberFormatted = str_pad($lastFileNumber, 2, '0', STR_PAD_LEFT);

            $lastFile = 'products_'.$lastFileNumberFormatted.'.json.gz' ?: 'products_01.json.gz';

            $url = 'https://challenges.coode.sh/food/data/jsona/'.$lastFile;
            $localPath = storage_path('app/'.$lastFile);
            $fileContent = file_get_contents($url);
            
            if ($fileContent) {

                file_put_contents($localPath, $fileContent);

                Log::channel('importdata')->info('Dados baixado com sucesso');

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

                    Log::channel('importdata')->info('Registrando dados');

                    $this->importDataRepository->create($produtos);

                    ImportData::updateOrCreate(
                        ['last_file_number' => $lastFileNumber],
                        ['status' => 'success']
                    );
                    
                    Log::channel('importdata')->info('Dados registrados com sucesso');
                    Log::channel('importdata')->info('Enviando alerta ao telegram');

                    $this->alertService->sendAlertToTelegram('success');
                }
            }
            else{
                Log::channel('importdata')->info('Não foram encontrados novos dados para importação');
                $this->alertService->sendAlertToTelegram('information');                
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

            Log::channel('importdata')->error('Falha ao importar dados: '.$e);

            Log::channel('importdata')->info('Enviando alerta ao telegram');
            $this->alertService->sendAlertToTelegram('fail');
        }
    }

    public function checkLastImportDataOpenFoodFacts(){

        $lastImport = ImportData::latest()->first();

        if($lastImport->status === 'fail'){

            Log::channel('importdata')->info('Inicializando nova tentativa para importar dados na data: '.$lastImport->created_at->format('Y-m-d H:i:s'));
            
            $this->importDataOpenFoodFacts();
        }
    }
}
