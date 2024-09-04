<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'libelle',
        'prix',
        'quantite',
        
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        
    ];

}
