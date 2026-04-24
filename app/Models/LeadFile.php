<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFile extends Model
{
    protected $fillable = [
        'lead_id', 'file_key', 'original_name', 'stored_name',
        'mime_type', 'file_size', 'download_token', 'download_url',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}