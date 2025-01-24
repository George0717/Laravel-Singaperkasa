<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'user_id',
        'description',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array', // Kolom 'changes' akan secara otomatis menjadi array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
