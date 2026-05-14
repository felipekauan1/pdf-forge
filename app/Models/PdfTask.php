<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfTask extends Model
{
    protected $fillable = [
        'operation',
        'status',
        'original_filename',
        'result_path',
        'error_message',
    ];
}
