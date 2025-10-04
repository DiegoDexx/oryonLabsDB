<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRequirement extends Model
{
    protected $fillable = ['project_id', 'field_id', 'value'];

    public $timestamps = false;


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function field()
{
    return $this->belongsTo(ProjectField::class, 'field_id', 'id');
}


}

