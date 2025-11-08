<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'date',
        'dette_id',
    ];

    // Relation Many-to-One avec Dette (Un paiement appartient à une dette)
    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
}
