<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $fillable = ['message_id', 'file_id'];

    public function message(){
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function file(){
        return $this->belongsTo(File::class, 'file_id');
    }
}
