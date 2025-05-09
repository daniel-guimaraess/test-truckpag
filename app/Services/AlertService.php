<?php

namespace App\Services;

use App\Mail\SendMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertService
{
    public function sendEmail($type) {
        try {
            if(env('ALERT_EMAIL'))
            {
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

                $nameAlert = "{$emoji} {$msg}";
                $now = Carbon::now();
                $data = $now->format('d/m/Y');
                $hora = $now->format('H:i:s');
                
                Mail::to(env('ALERT_EMAIL'), 'Monitoramento')->send(
                    new SendMail('Monitoramento', $nameAlert, $data, $hora)
                );
            }
            
        } catch (\Exception $e) {
            Log::error("Erro ao enviar o e-mail: " . $e->getMessage());
        }
    }

    public function sendAlertToTelegram(string $type){
        
        $chatId = env('CHAT_ID_TELEGRAM');
        $botToken = env('BOT_TOKEN_TELEGRAM');

        if($chatId && $botToken){

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

            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
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
