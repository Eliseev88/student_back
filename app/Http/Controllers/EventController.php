<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Event::with('category')
        ->orderBy('id', 'desc')->get();
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
        $category = Category::where('title', $request->input('category'))->first();

        $fields['title'] = $request->input('title');
        $fields['category_id'] = $category->id;
        $fields['user_id'] = $request->user()->id;
        $fields['description'] = $request->input('description');
        $fields['start'] = $request->input('start');
        $fields['finish'] = $request->input('finish');

        $newEvent = Event::create($fields);

        if ($newEvent) {
            return response($newEvent, 201);
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
        $existingEvent = Event::with('category', 'users')->find($id);

        if ($existingEvent) {
            return response($existingEvent, 200);
        }
        return response(['message' => 'Event not found'], 404);
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
        $existingEvent = Event::find($id);

        if ($existingEvent) {
            $category = Category::where('title', $request->input('category'))->first();

            $fields['title'] = $request->input('title');
            $fields['category_id'] = $category->id;
            $fields['user_id'] = $request->user()->id;
            $fields['description'] = $request->input('description');
            $fields['type'] = $request->input('type');
            $fields['start'] = $request->input('start');
            $fields['finish'] = $request->input('finish');

            $existingEvent->fill($fields)->save();

            return response($existingEvent, 200);
        }
        return response(['message' => 'Event not found'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $existingEvent = Event::find($id);

        if ($existingEvent) {
            $existingEvent->delete();

            return response(['message' => 'Delete successfull'], 202);
        }
        return response(['message' => 'Event not found'], 404);
    }
}
