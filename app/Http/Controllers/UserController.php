<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
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
        return view('redis.create');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function redisShow()
    {
        return view('redis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function redisStore(Request $request)
    {
        $attributes = request()->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            // 'password' => 'required|min:5|max:255',
        ]);

        // dd($request->all());
        // dd(Redis::keys('user:*'));
    

        // Assuming you have user data from a form or elsewhere
        $userData = [
            'id' => $this->getLastUserId() + 1,
            'name' => $request->name,
            'email' => $request->password,
            // Add more user data fields as needed
        ];

        $userId = $userData['id']; // Assuming 'id' is the unique identifier for the user
        
        

        // saving into json
        // $json_key = 'user:profile:'.$userId;
        // Redis::set($json_key, json_encode($userData));

        // Store the user data in a Redis hash
        $redisKey = 'user:' . $userId; 
        Redis::hmset($redisKey, $userData);

        // return 'User created and data stored in Redis.';

        return redirect()->route('redis-show-all');
    }

    public function getLastUserId()
    {
        $keys = Redis::keys('user:*');
        // dd($keys);

        if (!empty($keys)) {
            $lastKey = end($keys);
            $lastUserId = str_replace('laravel_database_user:', '', $lastKey);
            return $lastUserId;
        }

        return 0 ; // No matching keys found
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function redisGetUserHash($userId)
    {
        $redisKey = 'user:' . $userId;
        $userData = Redis::hgetall($redisKey);

        // Check if user data exists in Redis
        if (empty($userData)) {
            return null; // or throw an exception, depending on your application's logic
        }

        return $userData;
    }

    public function redisGetUserProfile($userId)
    {
        $jsonKey = 'user:profile:' . $userId;
        $userDataJson = Redis::get($jsonKey);

        // Check if user data exists in Redis
        if (!$userDataJson) {
            return null; // or throw an exception, depending on your application's logic
        }

        // Convert JSON string to associative array
        $userData = json_decode($userDataJson, true);

        return $userData;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $userData = $this->redisGetUserProfile($id);
        $userData2 = $this->redisGetUserHash($id);

        // dd($userData,$userData2);

        $keys = Redis::keys('user:*');
        // dd($keys);
        $users = [];
        
        foreach ($keys as $key) {
            // Remove the 'user:' prefix to get the user ID
            // dd($key);
            try{
                $userId = str_replace('laravel_database_', '', $key);

                // Add the user data to the array
                // $userData = Redis::get($key);
                $userData = Redis::hgetall($userId);
                // dd($userData);

                // If the user data is found in Redis, add it to the array
                if ($userData) {
                    $users[$userId] = $userData;
                }
            } catch(Exception $e){

            }
            
            
    
        }
    
        return $users;
    }

     /**
     * Display the specified resource.
     */
    public function showAll()
    {
        
        $keys = Redis::keys('user:*');
        // dd($keys);
        $users = [];
        
        foreach ($keys as $key) {
            // Remove the 'user:' prefix to get the user ID
            // dd($key);
            try{
                $userId = str_replace('laravel_database_', '', $key);

                // Add the user data to the array
                // $userData = Redis::get($key);
                $userData = Redis::hgetall($userId);
                // dd($userData);

                // If the user data is found in Redis, add it to the array
                if ($userData) {
                    $users[$userId] = $userData;
                }
            } catch(Exception $e){

            }
            
            
    
        }
    
        return $users;
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
