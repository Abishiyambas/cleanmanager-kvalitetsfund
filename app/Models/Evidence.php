<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    protected $table = 'evidence';

    protected $fillable = ['corrective_action_id', 'user_id', 'note', 'photo_path'];

    public function action()
    {
        return $this->belongsTo(CorrectiveAction::class, 'corrective_action_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
