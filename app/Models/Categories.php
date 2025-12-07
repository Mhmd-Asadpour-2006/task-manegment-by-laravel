<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Categories extends Model
{
    use HasApiTokens,HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'title',
        'user_id',
        'id'
    ];
}
