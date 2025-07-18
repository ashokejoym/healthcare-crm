<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        // Check doctor availability
        $conflict = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->where('appointment_time', $data['appointment_time'])
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Doctor not available at this time'], 409);
        }

        $appointment = Appointment::create($data);

        return response()->json($appointment, 201);
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

        if (
            !$user->hasRole(['Admin', 'CRM Agent']) &&
            !($user->hasRole('Doctor') && $appointment->doctor->user_id === $user->id)
        ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'appointment_date' => 'nullable|date',
            'appointment_time' => 'nullable',
            'status' => 'nullable|in:Scheduled,Confirmed,Completed,Cancelled,No-Show',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($data);

        return response()->json($appointment);
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
