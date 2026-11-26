<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $fillable = ['location_id', 'template_id', 'inspector_id', 'notes'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function findings()
    {
        return $this->hasMany(Finding::class);
    }
}
