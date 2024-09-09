<?php

namespace App\Providers;

use Pusher\Pusher;

class PusherServiceProvider extends Pusher
{
    public function __construct() {
        parent::__construct(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => env('PUSHER_APP_TLS')
            ]
        );
    }
}

