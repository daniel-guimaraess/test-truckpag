<?php

namespace App\Repositories;

use App\Models\Alert;
use App\Models\Product;

class AlertRepository
{   
    public function getAlert(){      

        return Alert::select('id', 'chat_id', 'bot_token')->first();
    }

    public function update(Alert $alert, $request){

        $alert->update([
            "chat_id" => $request["chat_id"],
            "bot_token" => $request["bot_token"]
        ]);
    }
}
