<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Numero extends Model
{
    protected $table = 'numeros';

    protected $fillable = [
        'numero',
        'estado',
        'pedido_id',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
