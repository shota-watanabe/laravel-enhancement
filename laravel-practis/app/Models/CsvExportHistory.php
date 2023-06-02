<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsvExportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'download_user_id',
        'file_name',
    ];

    public function download_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'download_user_id');
    }
}
