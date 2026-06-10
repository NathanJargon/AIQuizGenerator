<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'stored_name',
        'file_path',
        'file_size',
        'extracted_text',
    ];
}