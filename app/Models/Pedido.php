<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'bot_contact_id',
        'cliente_id',
        'plan_id',
        'cliente',
        'telefono',
        'estado',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function botContact(): BelongsTo
    {
        return $this->belongsTo(BotContact::class);
    }

    public function clienteRelacionado(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function numero(): HasOne
    {
        return $this->hasOne(Numero::class);
    }
}
