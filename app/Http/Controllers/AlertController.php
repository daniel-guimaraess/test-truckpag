<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AlertService;

class AlertController extends Controller
{
    private $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function getAlert()
    {   
        return $this->alertService->getAlert();
    }
    
    public function update(Request $request, $id)
    {        
        return $this->alertService->update($id, $request);              
    }
}
