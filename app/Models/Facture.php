<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'type_document',   // pro-forma | recu
        'numero_facture',
        'total',
        'modifiable'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneFacture::class);
    }

    public function isDefinitive()
    {
        return $this->type_document === 'recu';
    }

    public static function generateNumeroFor(string $type_document)
    {
        $prefix = $type_document === 'pro-forma' ? 'PF' : 'R';
        $base = $prefix . date('Ymd');

        $last = self::where('numero_facture', 'like', $base.'%')
            ->orderByDesc('numero_facture')
            ->first();

        if (!$last) {
            return $base.'001';
        }

        $lastNumber = (int) substr($last->numero_facture, -3);

        return $base . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }
}
