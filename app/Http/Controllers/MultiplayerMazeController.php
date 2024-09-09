<?php

namespace App\Http\Controllers;

use App\Events\AllReadyEvent;
use App\Events\CommunicationEvent;
use App\Models\Maze;
use App\Providers\PusherServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Pusher\Pusher;

class MultiplayerMazeController extends Controller
{
    public function getMultiplayerMaze(Request $request, Maze $maze)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $cacheKey = 'maze_' . $maze->id;

        $users = Cache::get($cacheKey, []);

        $users[$data['name']] = false;

        Cache::put($cacheKey, $users);

        (new PusherServiceProvider())->trigger("multi-player." . $maze->id, "UserAdded", ['user' => $data['name'], 'users' => $users]);

        return response()->json([
            'maze' => json_decode($maze->maze),
            'users' => $users
        ]);
    }


    public function beReady(Request $request, Maze $maze)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $cacheKey = 'maze_' . $maze->id;

        $users = Cache::get($cacheKey, []);

        $users[$data['name']] = true;

        Cache::put($cacheKey, $users);

        $are_all_ready = true;

        foreach ($users as $user) {
            $are_all_ready = $are_all_ready && $user;
        }

        (new PusherServiceProvider())->trigger("multi-player." . $maze->id, "AllUsersReady", ['users' => $users, 'are_all_ready' => $are_all_ready]);


        return response()->json([
            'maze_id' => $maze->id,
            'users' => $users,
            'are_all_ready' => $are_all_ready
        ]);
    }

    public function finishMaze(Request $request, Maze $maze)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'time' => ['required', 'string', 'max:255'],
        ]);

        $cacheKey = 'maze_' . $maze->id;

        $users = Cache::get($cacheKey, []);

        $users[$data['name']] = ['finished' => true, 'time' => $data['time']];

        Cache::put($cacheKey, $users);

        broadcast(new CommunicationEvent($data['name'], $data['time'], $maze->id))->toOthers();

        (new PusherServiceProvider())->trigger("multi-player." . $maze->id, "UserFinished", ['user' => $data['name'], 'time' => $data['time']]);

        foreach ($users as $user) {
            if(isset($user['finished'])) {
                Cache::delete($cacheKey);
            }
        }

        return response()->json([
            'maze_id' => $maze->id,
            'user' => $data['name'],
            'time' => $data['time']
        ]);
    }
}
