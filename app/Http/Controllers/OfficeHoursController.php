<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeHourSlotRequest;
use App\Models\Enrollment;
use App\Models\OfficeHourBooking;
use App\Models\OfficeHourSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeHoursController extends Controller
{
    /**
     * Teacher view: Manage office hour slots & view upcoming student bookings
     */
    public function teacherIndex()
    {
        $teacher = Auth::user();

        $slots = OfficeHourSlot::where('teacher_id', $teacher->id)
            ->withCount(['bookings' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $bookings = OfficeHourBooking::whereHas('slot', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
            ->with(['slot', 'student'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('portal.teacher_office_hours', compact('slots', 'bookings'));
    }

    /**
     * Teacher: Create new office hour slot
     */
    public function storeSlot(StoreOfficeHourSlotRequest $request)
    {
        $validated = $request->validated();

        $validated['teacher_id'] = Auth::id();
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = true;

        OfficeHourSlot::create($validated);

        return redirect()->back()->with('success', 'Qəbul saatı pəncərəsi uğurla yaradıldı.');
    }

    /**
     * Teacher: Delete slot
     */
    public function destroySlot($id)
    {
        $slot = OfficeHourSlot::where('teacher_id', Auth::id())->findOrFail($id);
        $slot->delete();

        return redirect()->back()->with('success', 'Qəbul saatı pəncərəsi silindi.');
    }

    /**
     * Teacher: Update booking status (confirmed, completed, cancelled)
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        $booking = OfficeHourBooking::whereHas('slot', function ($q) {
            $q->where('teacher_id', Auth::id());
        })->findOrFail($id);

        $booking->update($validated);

        return redirect()->back()->with('success', 'Görüş statusu yeniləndi.');
    }

    /**
     * Student view: Browse available slots & make a booking
     */
    public function studentIndex()
    {
        $student = Auth::user();

        // Get teachers for sections where student is enrolled
        $myTeachers = User::whereHas('taughtSections.enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        // Get slots of these teachers
        $teacherIds = $myTeachers->pluck('id');
        $availableSlots = OfficeHourSlot::whereIn('teacher_id', $teacherIds)
            ->where('is_active', true)
            ->with('teacher')
            ->get();

        $myBookings = OfficeHourBooking::where('student_id', $student->id)
            ->with(['slot.teacher'])
            ->orderBy('booking_date', 'desc')
            ->get();

        return view('portal.student_office_hours', compact('myTeachers', 'availableSlots', 'myBookings'));
    }

    /**
     * Student: Store new booking
     */
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'slot_id' => 'required|exists:office_hour_slots,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'topic' => 'required|string|max:500',
        ]);

        $slot = OfficeHourSlot::where('is_active', true)->findOrFail($validated['slot_id']);

        $isTaughtByTeacher = Enrollment::where('student_id', Auth::id())
            ->whereHas('courseSection', fn ($q) => $q->where('teacher_id', $slot->teacher_id))
            ->exists();

        if (! $isTaughtByTeacher) {
            abort(403, 'Yalnız dərs aldığınız müəllimin qəbul saatına yazıla bilərsiniz.');
        }

        $alreadyBooked = OfficeHourBooking::where('slot_id', $slot->id)
            ->where('student_id', Auth::id())
            ->whereDate('booking_date', $validated['booking_date'])
            ->exists();

        if ($alreadyBooked) {
            return redirect()->back()->with('error', 'Bu tarix üçün artıq həmin qəbul saatına yazılmısınız.');
        }

        // Check if slot already reached capacity for that date
        $existingCount = OfficeHourBooking::where('slot_id', $slot->id)
            ->whereDate('booking_date', $validated['booking_date'])
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($existingCount >= $slot->max_students_per_slot) {
            return redirect()->back()->with('error', 'Bu tarix üçün qəbul limiti dolmuşdur. Lütfən başqa tarix seçin.');
        }

        OfficeHourBooking::create([
            'slot_id' => $slot->id,
            'student_id' => Auth::id(),
            'booking_date' => $validated['booking_date'],
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'topic' => $validated['topic'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Müəllimlə qəbul müraciətiniz qeydə alındı.');
    }
}
