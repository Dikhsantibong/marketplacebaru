<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;
    protected $table = 'kontak';
    public $timestamps = false;
    protected $fillable = [
        'username',
        'email',
        'subjek',
        'pesan',
    ];
}
