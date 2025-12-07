<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tasks extends Model
{
    use HasApiTokens,HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'priority',
        'date_of_completion',
        'status',
        'category'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tasks_users', 'task_id', 'user_id');
    }

}
