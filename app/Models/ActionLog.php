<?php

namespace App\Models;

use App\Enums\ActionStatus;
use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    protected $fillable = ['corrective_action_id', 'user_id', 'event', 'from_status', 'to_status', 'comment'];

    protected function casts(): array
    {
        return [
            'from_status' => ActionStatus::class,
            'to_status' => ActionStatus::class,
        ];
    }

    public function action()
    {
        return $this->belongsTo(CorrectiveAction::class, 'corrective_action_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
