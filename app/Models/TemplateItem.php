<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateItem extends Model
{
    protected $fillable = ['template_id', 'category_id', 'text', 'position'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
