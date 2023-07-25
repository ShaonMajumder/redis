<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Assuming you have user data from a form or elsewhere
        $userData = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            // Add more user data fields as needed
        ];

        $userId = $userData['id']; // Assuming 'id' is the unique identifier for the user
        
        

        // saving into json
        $json_key = 'user:profile:'.$userId;
        Redis::set($json_key, json_encode($userData));

        // Store the user data in a Redis hash
        $redisKey = 'user:' . $userId; 
        Redis::hmset($redisKey, $userData);

        return 'User created and data stored in Redis.';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        
        $redisKey = 'user:profile:' . $id;
        $user = Redis::get($redisKey); //return as json

        // Retrieve all fields and their values for the user from the Redis hash
        $userData = Redis::hgetall('user:'.$id);

        dd($user, $userData);
 
        // return view('user.profile', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * Test the conn
     */
    public function test()
    {
        try{
            $redis=Redis::connect('127.0.0.1',3306);
            return response('redis working');
        }catch(\Predis\Connection\ConnectionException $e){
            return response('error connection redis');
        }
    }
}
