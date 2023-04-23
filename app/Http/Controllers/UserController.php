<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::orderBy('name')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $fields['name'] = $request->input('name');
        $fields['email'] = $request->input('email');
        $fields['role'] = 'guest';
        $fields['password'] = Hash::make($request->input('password'));
        $fields['remember_token'] = Str::random(100);

        $isEmailBusy = User::where('email', $fields['email'])->first();

        if ($isEmailBusy) {
            return response(['message' => 'Email is busy'], 409);
        }

        $newUser = User::create($fields);

        if ($newUser) {
            return response($newUser, 201);
        }
        return response(['message' => 'Request error'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $existingUser = User::with('takingPart', 'events')->find($id);

        if ($existingUser) {
            return response($existingUser, 200);
        }
        return response(['message' => 'User not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existingUser = User::find($id);

        if ($existingUser) {
            $fields['role'] = $request->input('role');
            // $fields['name'] = $request->input('name');
            // $fields['email'] = $request->input('email');
            // $fields['password'] = Hash::make($request->input('password'));

            $existingUser->fill($fields)->save();

            return response($existingUser, 200);
        }
        return response(['message' => 'User not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existingUser = User::find($id);

        if ($existingUser) {
            $existingUser->delete();

            return response(['message' => 'Delete successfull'], 202);
        }
        return response(['message' => 'User not found'], 404);
    }

    public function writeToEvent($userId, $eventId) {
        $user = User::find($userId);

        if ($user) {
            $user->takingPart()->attach($eventId);
            return response(['message' => 'Write successfull'], 202);
        }
        return response(['message' => 'User not found'], 404);
    }

    public function getUserEvents(Request $request) {
        $events = $request->user()->takingPart()->get();
        return response($events, 200);
    }

    public function unsignUserFromEvent(Request $request, $eventId) {
        $request->user()->takingPart()->detach($eventId);
        return response(['message' => 'detached'], 200);
    } 
    
    public function signUserToEvent(Request $request, $eventId) {
        $request->user()->takingPart()->attach($eventId);
        return response(Event::with('category')->find($eventId), 200);
        
    } 
    public function getUserByToken(Request $request) {
        $user = Auth::user();
        if ($user) {
            $response = [
                'user' => $user,
                'token' => $user->remember_token,
            ];
            return response($response, 200);
        }
        return response(['message' => 'Invalid token'], 498);
    }
}
