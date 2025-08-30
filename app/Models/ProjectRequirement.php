<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRequirement extends Model
{

    protected $fillable = ['project_id', 'field_name', 'field_value'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function field()
{
    return $this->belongsTo(ProjectField::class, 'field_name', 'field_name');
}


}
