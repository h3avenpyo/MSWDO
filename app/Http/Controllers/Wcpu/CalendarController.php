<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\Event;
use App\Models\Wcpu\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        
        $events = Event::forUser($user)
            ->with('creator')
            ->orderBy('event_date', 'asc')
            ->get()
            ->each(function (Event $event) {
                $eventDate = $event->event_date?->timezone(config('app.timezone'));
                $event->setAttribute(
                    'calendar_date',
                    $eventDate?->toDateString()
                );
                // FullCalendar's named timezone support is UTC-coerced here because
                // the page does not load a timezone plugin. Pass Manila wall time
                // without an offset so early events stay on their saved calendar day.
                $event->setAttribute('calendar_start', $eventDate?->format('Y-m-d\TH:i:s'));
            });
        
        $view = $isAdmin 
            ? 'admin-tailwind.calendar.index' 
            : 'desk-officer.calendar.index';
        
        return view($view, compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'location' => 'nullable|string|max:255',
            'end_time' => 'nullable|date|after:event_date',
            'target_audience' => 'required|in:all,admins_only,bcpc_only,vawc_only',
        ]);

        $eventDate = $request->event_date;
        
        // Check if date is in the past
        if (strtotime($eventDate) < strtotime(date('Y-m-d'))) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create events in the past.'
            ], 422);
        }

        // Check for existing events on the same date
        $existingEvents = Event::forUser(Auth::user())
            ->whereDate('event_date', $eventDate)
            ->count();

        if ($existingEvents > 0) {
            return response()->json([
                'success' => false,
                'message' => 'There is already an event on this date. Do you want to continue?',
                'has_conflict' => true
            ], 200);
        }

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $eventDate,
            'location' => $request->location,
            'end_time' => $request->end_time,
            'target_audience' => $request->target_audience,
            'created_by' => Auth::id(),
        ]);

        // Log activity for notification
        activity()
            ->causedBy(Auth::user())
            ->performedOn($event)
            ->withProperties([
                'event_title' => $event->title,
                'event_date' => $event->event_date,
                'target_audience' => $event->target_audience,
            ])
            ->log('created calendar event: ' . $event->title);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully.',
            'event' => $event->load('creator')
        ]);
    }

    public function storeWithConflict(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'location' => 'nullable|string|max:255',
            'end_time' => 'nullable|date|after:event_date',
            'target_audience' => 'required|in:all,admins_only,bcpc_only,vawc_only',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'end_time' => $request->end_time,
            'target_audience' => $request->target_audience,
            'created_by' => Auth::id(),
        ]);

        // Log activity for notification
        activity()
            ->causedBy(Auth::user())
            ->performedOn($event)
            ->withProperties([
                'event_title' => $event->title,
                'event_date' => $event->event_date,
                'target_audience' => $event->target_audience,
            ])
            ->log('created calendar event: ' . $event->title);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully.',
            'event' => $event->load('creator')
        ]);
    }

    public function show($id)
    {
        $event = Event::forUser(Auth::user())->with('creator')->findOrFail($id);
        
        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        
        // Authorization check - only creator or admin can delete
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        
        abort_unless($event->created_by === $user->id || $isAdmin, 403);
        
        $event->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully.'
        ]);
    }
}
