<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\EventResource;
use App\Models\Event as CustomEvent;
use App\Models\EventHost as Host;
use App\Models\User;

class EventController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponse(EventResource::collection(CustomEvent::paginate()), 'RETRIEVE_SUCCESS');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required|max:2000',
            'venue' => 'required',
            'image' => 'nullable',
            'startDate' => 'required',
            'endDate' => 'required',
            'hosts' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if hosts exist, if not create new entries
        $hosts = [];
        $hosts = explode(',', $request->hosts);
        
        $host_array = []; // this is to receive new created models
        
        foreach($hosts as $host) {
            $user = User::where('name', $host)->first();
            if (is_null($user)) {
                User::create([
                    'name' => $host,
                    'email' => strtolower(preg_replace('/\s+/', '', $host)) . '@example.com',
                    'role_id' => 5, // member
                    'password' => bcrypt($host),
                ]);
            }
            
            array_push($host_array, $user->id);
        }
        // var_dump($host_array); die;

        // manipulate image storage
        $image_path = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_new_name = time() . $image->getClientOriginalExtension();
            $image->storeAs('uploads/events', $image_new_name);
        }
        
        $event = CustomEvent::create([
            'name' => $request->name,
            'description' => $request->description,
            'venue' => $request->venue,
            'image' => $image_path ? $image_path : NULL,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
        ]);
        // var_dump($event); die;


        // save the new event hosts
        // $event->hosts()->saveMany($host_array);
        for ($i = 0; $i < count($host_array); $i++) {
            Host::firstOrCreate([
                'event_id' => $event->id,
                'user_id' => $host_array[$i],
            ]);
        }

        if (is_null($event)) {
            return $this->sendError('CREATE_FAILED');
        } else {
            return $this->sendResponse(new EventResource($event), 'CREATE_SUCCESS');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = CustomEvent::find($id);

        if (is_null($event)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            return $this->sendResponse(new EventResource($event), 'RETRIEVE_SUCCESS');
        }
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
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'description' => 'nullable|max:2000',
            'venue' => 'nullable',
            'image' => 'nullable',
            'startDate' => 'nullable',
            'endDate' => 'nullable',
            'hosts' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        try {
            // check if hosts exist, if not create new entries
            $hosts = [];
            $hosts = explode(',', $request->hosts);
            
            $host_array = []; // this is to receive new created models
            
            foreach($hosts as $host) {
                $user = User::where('name', $host)->first();
                if (is_null($user)) {
                    User::create([
                        'name' => $host,
                        'email' => strtolower(preg_replace('/\s+/', '', $host)) . '@example.com',
                        'role_id' => 5, // member
                        'password' => bcrypt($host),
                    ]);
                }
                
                array_push($host_array, $user->id);
            }
            // var_dump($host_array); die;

            // manipulate image storage
            $image_path = '';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image_new_name = time() . $image->getClientOriginalExtension();
                $image->storeAs('uploads/events', $image_new_name);
            }
            
            $event = CustomEvent::find($id);

            if (is_null($event)) {
                return $this->sendError('UPDATE_FAILED');
            } else {
                $event->update([
                    'name' => $request->name ? $request->name : $event->name,
                    'description' => $request->description ? $request->description : $event->description,
                    'venue' => $request->venue ? $request->venue : $event->venue,
                    'image' => $image_path ? $image_path : $event->image,
                    'start_date' => $request->startDate ? $request->startDate : $event->start_date,
                    'end_date' => $request->endDate ? $request->endDate : $event->end_date,
                ]);

                for ($i = 0; $i < count($host_array); $i++) {
                    Host::firstOrCreate([
                        'event_id' => $event->id,
                        'user_id' => $host_array[$i],
                    ]);
                }

                return $this->sendResponse(new EventResource($event), 'UPDATE_SUCCESS');
            }
        } catch (\Throwable $th) {
            return $this->sendError('UPDATE_FAILED', $th->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = CustomEvent::find($id);

        if (is_null($event)) {
            return $this->sendError('NOT_FOUND');
        }

        if ($event->eventHosts->count() > 0) {
            foreach ($event->eventHosts as $host) {
                $host->delete();
            }
        }

        if ($event->delete()) {
            return $this->sendResponse(new EventResource($event), 'DELETE_SUCCESS', 204);
        } else {
            return $this->sendError('DELETE_FAILED');
        }
    }
}
