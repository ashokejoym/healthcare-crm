<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function index()
    {
        $this->authorizeRole(['Admin', 'CRM Agent']);
        return response()->json(Appointment::with(['patient', 'doctor'])->get());
    }

  

public function store(Request $request)
{
    $this->authorizeRole(['Admin', 'CRM Agent']);

    // Validation rules
    $rules = [
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:doctors,id',
        'appointment_date' => 'required|date|after_or_equal:today',
      'appointment_time' => [
        'required',
        'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/', // HH:MM or HH:MM:SS 24-hour format
    ],
        'notes' => 'nullable|string|max:1000',
    ];

    // Optional: Custom error messages
    $messages = [
        'appointment_date.after_or_equal' => 'Appointment date must be today or in the future.',
        'appointment_time.date_format' => 'Time must be in the format HH:MM (24-hour).',
    ];

  $validator = Validator::make($request->all(), $rules, $messages);

if ($validator->fails()) {
    return response()->json([
        'message' => 'Validation failed.',
        'errors' => $validator->errors()
    ], 422);
}


    $data = $validator->validated();

    // Doctor availability check
    $conflict = Appointment::where('doctor_id', $data['doctor_id'])
        ->where('appointment_date', $data['appointment_date'])
        ->where('appointment_time', $data['appointment_time'])
        ->exists();

    if ($conflict) {
        return response()->json([
            'error' => 'Doctor is not available at the selected date and time.'
        ], 409);
    }

    $appointment = Appointment::create($data);

    return response()->json([
        'message' => 'Appointment created successfully.',
        'appointment' => $appointment
    ], 201);
}


    public function showByPatient($patient_id)
    {
        $user = Auth::user();

        if (
            $user->hasRole(['Admin', 'CRM Agent']) ||
            ($user->hasRole('Patient') && $user->email === Patient::findOrFail($patient_id)->email) ||
            ($user->hasRole('Doctor') && Appointment::where('patient_id', $patient_id)->where('doctor_id', $user->doctor->id)->exists())
        ) {
            return response()->json(Appointment::where('patient_id', $patient_id)->with(['doctor'])->get());
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function showByDoctor($doctor_id)
    {
        $user = Auth::user();

        if (
            $user->hasRole(['Admin', 'CRM Agent']) ||
            ($user->hasRole('Doctor') && $user->doctor && $user->doctor->id == $doctor_id)
        ) {
            return response()->json(Appointment::where('doctor_id', $doctor_id)->with(['patient'])->get());
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function update(Request $request, $id)
{
    $appointment = Appointment::findOrFail($id);
    $user = Auth::user();

    // Role-based access control
    if (
        !$user->hasRole(['Admin', 'CRM Agent']) &&
        !($user->hasRole('Doctor') && $appointment->doctor->user_id === $user->id)
    ) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Validate request
    $data = $request->validate([
        'appointment_date' => 'nullable|date|after_or_equal:today',
        'appointment_time' => 'nullable|date_format:H:i',
        'status' => 'nullable|in:Scheduled,Confirmed,Completed,Cancelled,No-Show',
        'notes' => 'nullable|string|max:1000',
    ]);

    // Check for doctor time conflict if date/time is being changed
    if (!empty($data['appointment_date']) && !empty($data['appointment_time'])) {
        $conflict = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $data['appointment_date'])
            ->where('appointment_time', $data['appointment_time'])
            ->where('id', '!=', $appointment->id) // Exclude current
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Doctor not available at this time'], 409);
        }
    }

    $appointment->update($data);

    return response()->json([
        'message' => 'Appointment updated successfully.',
        'data' => $appointment
    ]);
}


    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        if (!Auth::user()->hasRole(['Admin', 'CRM Agent'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment cancelled']);
    }

    protected function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
