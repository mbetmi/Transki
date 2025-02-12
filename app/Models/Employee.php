<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'password', 'department_name', 'type'];

    // public function messages(){
    //     return $this->hasMany(Message::class);
    // }

     // Relation avec les messages envoyés
     public function sentMessages()
     {
         return $this->hasMany(Message::class, 'sender_id');
     }
 
     // Relation avec les messages reçus
     public function receivedMessages()
     {
         return $this->hasMany(Message::class, 'receiver_id');
     }
}
