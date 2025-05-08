<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportData extends Model
{
    protected $fillable = [
        "last_file_number",
        "status"
    ];
}
