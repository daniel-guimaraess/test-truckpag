<?php

namespace App\Services;

use App\Models\ImportData;
use Illuminate\Support\Facades\DB;

class CheckApiService
{
    public function checkApi(){
        
        try {
            $dbStatus = $this->checkDatabaseConnection();

            $lastCronRun = $this->getLastCronRunTime();

            $onlineTime = $this->getOnlineTime();

            $memoryUsage = $this->getMemoryUsage();

            return response()->json([
                'database_connection' => $dbStatus,
                'last_cron_run' => $lastCronRun,
                'online_time' => $onlineTime,
                'memory_usage' => $memoryUsage,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Unable to fetch api status'
            ], 500);
        }
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();

            return 'Database connection is OK';

        } catch (\Exception $e) {

            return 'Database connection failed: ' . $e->getMessage();
        }
    }

    private function getLastCronRunTime()
    {
        $lastImport = ImportData::latest()->first();
        
        if (!$lastImport || !$lastImport->created_at) {
            return null;
        }
    
        return $lastImport->created_at->format('Y-m-d H:i:s');
    }

    private function getOnlineTime()
    {
        $uptime = shell_exec('uptime -p');

        return $uptime;
    }

    private function getMemoryUsage()
    {
        return memory_get_usage();
    }
}
