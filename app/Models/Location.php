<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    protected $fillable = ['name', 'address', 'description'];

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function findings()
    {
        return $this->hasMany(Finding::class);
    }
}
