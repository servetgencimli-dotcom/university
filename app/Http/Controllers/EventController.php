<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $events = $query->orderBy('start_time', 'asc')->paginate(9)->withQueryString();

        return view('pages.events.index', compact('events'));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $relatedEvents = Event::where('id', '!=', $event->id)->take(3)->get();

        return view('pages.events.show', compact('event', 'relatedEvents'));
    }

    public function rsvp(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
        ]);

        $ticketToken = 'TCK-'.strtoupper(Str::random(8));

        EventRsvp::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'ticket_token' => $ticketToken,
            'status' => 'confirmed',
        ]);

        $event->increment('registered_count');

        return redirect()->route('events.show', $event->slug)
            ->with('success', "Qeydiyyat uğurla tamamlandı! Rəqəmsal Bilet Kodunuz: {$ticketToken}");
    }
}
