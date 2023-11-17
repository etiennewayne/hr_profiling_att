<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $primaryKey = 'event_id';


    protected $fillable = [
        'event_datetime',
        'event_title',
        'event_desc',
        'img_path'
    ];



}
