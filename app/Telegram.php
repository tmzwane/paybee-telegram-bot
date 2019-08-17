<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    protected $fillable = ['user_id','username', 'command', 'default_setting', 'is_active'];
}
