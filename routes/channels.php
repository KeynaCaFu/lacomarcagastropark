<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado por orden: solo el cliente dueño puede suscribirse
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    return \DB::table('tbuser_order')
        ->where('order_id', (int) $orderId)
        ->where('user_id', $user->getKey())
        ->exists();
});
