<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\EventResource;
use App\Http\Resources\EventRegistrationResource;
use App\Models\Event as CustomEvent;
use App\Models\EventHost as Host;
use App\Models\User;
use App\Models\EventRegistration;

/**
 * @group Event management
 *
 * APIs for managing events
 */
class EventController extends BaseController
{
    /**
     * Get all events
     *
     * @unauthenticated
     *
     * This endpoint retrieves all events paginated in chunks of 15.
     *
     * @queryParam page The page number to retrieve. Example: 1
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = 15;
        return $this->sendResponse(EventResource::collection(CustomEvent::latest('updated_at')->paginate($per_page)), 'RETRIEVE_SUCCESS');
    }

    /**
     * Create a new event
     *
     * This endpoint creates a new event.
     *
     * @bodyParam name string required The name of the event. Example: Event 1
     * @bodyParam description string required The description of the event. Example: This is an event
     * @bodyParam venue string required The venue of the event. Example: Dar es Salaam
     * @bodyParam image string The image of the event. Example: image.jpg
     * @bodyParam startDate string required The start date of the event. Example: 2021-01-01
     * @bodyParam endDate string required The end date of the event. Example: 2021-01-01
     * @bodyParam hosts string required The hosts of the event separated by commas and single whitespaces. Example: John Doe, Jane Doe
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

        try {
            // check if hosts exist, if not create new entries
            $hosts = [];
            $hosts = explode(', ', $request->hosts);

            $host_array = []; // this is to receive new created models

            foreach($hosts as $host) {
                $user = User::where('name', $host)->first();
                if (is_null($user)) {
                    $user = User::create([
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
            // $image_path = '';
            // if ($request->hasFile('image')) {
            //     $image = $request->file('image');
            //     $image_new_name = time() . $image->getClientOriginalExtension();
            //     $image->storeAs('uploads/events', $image_new_name);
            // }

            $event = CustomEvent::create([
                'name' => $request->name,
                'description' => $request->description,
                'venue' => $request->venue,
                // 'image' => $image_path ? $image_path : $event->image, // made way for cloudinary image management from frontend
                'image' => $request->image ? $request->image : '',
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

        } catch (\Throwable $th) {
            return $this->sendError('CREATE_FAILED', $th->getMessage());
        }
    }

    /**
     * Get a single event
     *
     * @unauthenticated
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
     * Update an event
     *
     * This endpoint updates an event.
     *
     * @urlParam id required The ID of the event. Example: 1
     *
     * @bodyParam name string The name of the event. Example: Event 1
     * @bodyParam description string The description of the event. Example: This is an event
     * @bodyParam venue string The venue of the event. Example: Dar es Salaam
     * @bodyParam image string The image of the event. Example: image.jpg
     * @bodyParam startDate string The start date of the event. Example: 2021-01-01
     * @bodyParam endDate string The end date of the event. Example: 2021-01-01
     * @bodyParam hosts string The hosts of the event separated by commas and single whitespaces. Example: John Doe, Jane Doe
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
            $hosts = explode(', ', $request->hosts);

            $host_array = []; // this is to receive new created models

            foreach($hosts as $host) {
                $user = User::where('name', $host)->first();
                if (is_null($user)) {
                    $user = User::create([
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
            // $image_path = '';
            // if ($request->hasFile('image')) {
            //     $image = $request->file('image');
            //     $image_new_name = time() . $image->getClientOriginalExtension();
            //     $image->storeAs('uploads/events', $image_new_name);
            // }

            $event = CustomEvent::find($id);
            $event->flushCache();

            if (is_null($event)) {
                return $this->sendError('UPDATE_FAILED');
            } else {
                $event->update([
                    'name' => $request->name ? $request->name : $event->name,
                    'description' => $request->description ? $request->description : $event->description,
                    'venue' => $request->venue ? $request->venue : $event->venue,
                    // 'image' => $image_path ? $image_path : $event->image, // made way for cloudinary image management from frontend
                    'image' => $request->image ? $request->image : $event->image,
                    'start_date' => $request->startDate ? $request->startDate : $event->start_date,
                    'end_date' => $request->endDate ? $request->endDate : $event->end_date,
                ]);

                // var_dump($event->eventHosts->count()); die;

                // remove current hosts from the db
                if ($event->eventHosts->count() > 0 && $request->has('hosts')) {
                    foreach ($event->eventHosts as $host) {
                        $host->delete();
                    }
                }

                // save the new event hosts
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
     * Delete an event
     *
     * This endpoint deletes an event and all it's relations completely.
     *
     * <aside class="notice"> <strong>NOTE:</strong> This action is irreversible </aside>
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

        // fail safe check if event has passed and has no
        // if ($event->start_date > )

        if ($event->eventHosts->count() > 0) {
            foreach ($event->eventHosts as $host) {
                $host->delete();
            }
        }

        // fail safe: delete all registered attendees
        if ($event->attendees->count() > 0) {
            foreach ($event->attendees as $attendee) {
                $attendee->delete();
            }
        }

        if ($event->delete()) {
            return $this->sendResponse(new EventResource($event), 'DELETE_SUCCESS', 204);
        } else {
            return $this->sendError('DELETE_FAILED');
        }
    }

    // User registration for events
    /**
     * Register a user for an event
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     */
    public function registerToEvent(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        try {

            // flow
            // check if user exists, if not create new user
            // check if user has registered for event, if yes, return error
            // else register user for event

            $user = User::where('name', $request->name)->first();

            if (is_null($user)) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email ?? strtolower(preg_replace('/\s+/', '', $request->name)) . '@example.com',
                    'phone' => $request->phone ?? '(255)600000000',
                    'role_id' => 6, // guest
                    'password' => bcrypt($request->name),
                ]);
            }

            $event = CustomEvent::find($id);

            // var_dump($event); die;

            if (is_null($event)) {
                return $this->sendError('NOT_FOUND');
            } else {
                // new or update
                $eventRegistration = EventRegistration::where('user_id', $user->id)->where('event_id', $event->id)->first();

                if (!is_null($eventRegistration)) {
                    return $this->sendError('ALREADY_REGISTERED');
                } else {
                    $eventRegistration = new EventRegistration();
                    $eventRegistration->user_id = $user->id;
                    $eventRegistration->event_id = $event->id;
                    $eventRegistration->status = 'pending';
                    $eventRegistration->save();

                    // TODO::send confirmation email
                }

            }

            return $this->sendResponse(new EventRegistrationResource($eventRegistration), 'REGISTER_SUCCESS');
        } catch (\Throwable $th) {
            return $this->sendError('REGISTER_FAILED', $th->getMessage());
        }
    }

    // get all registered users for an event
    /**
     * Get all registered users for an event
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     */
    public function getRegisteredUsers($id)
    {
        $event = CustomEvent::find($id);

        if (is_null($event)) {
            return $this->sendError('NOT_FOUND');
        } else {
            $eventRegistrations = EventRegistration::where('event_id', $event->id)->get();
            return $this->sendResponse(EventRegistrationResource::collection($eventRegistrations), 'RETRIEVE_SUCCESS');
        }
    }
}
