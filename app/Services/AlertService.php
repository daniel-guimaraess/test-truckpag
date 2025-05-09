<?php

namespace App\Services;

use App\Models\Alert;
use App\Repositories\AlertRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlertService
{
    protected $alertRepository;

    public function __construct(AlertRepository $alertRepository)
    {
        $this->alertRepository = $alertRepository;
    }

    public function getAlert(){

        try {
            return $this->alertRepository->getAlert();

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Failed to show alert configuration'
            ], 400);
        }
    }  

    public function update(int|string $id, Request $request){
        
        try {           
            if(!$alert = Alert::find($id)){
                return response()->json([
                    'message' => 'Alert not found'
                ], 404);
            }
   
            $this->alertRepository->update($alert, $request);

            return response()->json([
                'message' => 'Alert updated successfully'
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Failed to update alert',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function sendAlertToTelegram(string $type){

        $alert = $this->getAlert();

        if($alert){

            switch ($type) {
                case 'fail':
                    $emoji = '🚨';
                    $msg = 'Falha ao importar dados';
                    break;
                case 'success':
                    $emoji = '✅';
                    $msg = 'Sucesso ao importar dados';
                    break;
                case 'information':
                    $emoji = 'ℹ️';
                    $msg = 'Nenhum dado novo para importação';
                    break;
            }

            $now = Carbon::now();
            $data = $now->format('d/m/Y');
            $hora = $now->format('H:i:s');

            $nameAlert = "{$emoji} {$msg}";
            $message = "*{$nameAlert}*\n";
            $message .= "Data: *{$data}*\n";
            $message .= "Hora: *{$hora}*";

            $response = Http::post("https://api.telegram.org/bot{$alert->bot_token}/sendMessage", [
                'chat_id' => $alert->chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            if ($response->successful()) {
                Log::channel('importdata')->info('Alerta enviado com sucesso');
            } else {            
                Log::channel('importdata')->error('Falha ao enviar alerta', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
            
            }
        }else{
            Log::channel('importdata')->info('Nenhum canal do telegram cadastrado');
        }
    }
}
