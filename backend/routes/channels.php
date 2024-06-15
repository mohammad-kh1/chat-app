<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
} , ["guards" => ["web" , "sanctum"]]);

Broadcast::channel("online_users" , function($user){
    return $user;
} , ["guards" => ["web" , "sanctum"]]);
