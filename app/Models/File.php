<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
   
     protected $fillable = ['name','type', 'size'];

     public function messages(){
        return $this->belongsToMany(Message::class);
     }
}
