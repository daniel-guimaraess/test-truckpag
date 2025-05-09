<?php

namespace App\Http\Controllers;

use App\Services\CheckApiService;
use Illuminate\Http\Request;

class CheckApiController extends Controller
{      
    protected $checkApiService;

    public function __construct(CheckApiService $checkApiService)
    {
        $this->checkApiService = $checkApiService;
    }

    /**
    * @OA\Get(
    *   tags={"check api"},
    *   summary="Check status api",
    *   path="/api/checkapi",
    *   @OA\Response(
    *       response=200,
    *       description="OK",
    *       @OA\MediaType(
    *           mediaType="application/json"
    *       )
    *   )
    * )
    **/
    public function checkApi(){
        
        return $this->checkApiService->checkApi();
    }
}
