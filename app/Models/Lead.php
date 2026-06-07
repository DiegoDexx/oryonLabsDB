<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'nombre',
        'empresa',
        'email',
        'telefono',
        'canal',
        'reto',
        'volumen_clientes',
        'herramientas',
        'urgencia',
        'categoria',
        'project_name',
        'prioridad',
        'plan_sugerido',
        'resumen_comercial',
        'status',
        'notas',
        'client_id',
        'project_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
