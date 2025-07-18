<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
  use Illuminate\Validation\ValidationException;


use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class PatientController extends Controller
{
    // GET /api/patients?first_name=&last_name=&phone_number=
    public function index(Request $request)
    {
        $this->authorizeRole(['Admin', 'CRM Agent']);

        $query = Patient::query();

        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', "%{$request->first_name}%");
        }

        if ($request->filled('last_name')) {
            $query->where('last_name', 'like', "%{$request->last_name}%");
        }

        if ($request->filled('phone_number')) {
            $query->where('phone_number', $request->phone_number);
        }

        return response()->json($query->get());
    }


public function store(Request $request)
{
    try {
        $this->authorizeRole(['Admin', 'CRM Agent']);

        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'phone_number' => 'required|unique:patients',
            'email' => 'nullable|email|unique:patients',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'insurance_details' => 'nullable|array',
        ]);

        $data['patient_id'] = Str::uuid();
        $patient = Patient::create($data);

        PatientAudit::create([
            'user_id' => auth()->id(),
            'patient_id' => $patient->id,
            'action' => 'created',
            'new_values' => $patient->toArray(),
            'ip_address' => request()->ip(),
        ]);

      return response()->json([
    'status' => true,
    'message' => 'Patient created successfully.',
    'data' => $patient
], 201);


    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    }
}


    public function show($id)
    {
        $patient = Patient::where('patient_id', $id)->firstOrFail();

        $user = Auth::user();

        // Admin, CRM Agent, Doctor can view all; Patient can only view their own (based on email)
        if (
            $user->hasRole(['Admin', 'CRM Agent', 'Doctor']) ||
            ($user->hasRole('Patient') && $patient->email === $user->email)
        ) {
            return response()->json($patient);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }


public function update(Request $request, $id)
{
    try {
        $this->authorizeRole(['Admin', 'CRM Agent']);

        // Find patient by UUID
        $patient = Patient::where('patient_id', $id)->firstOrFail();

        // Validate request
        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date',
            'gender' => 'sometimes|required|in:Male,Female,Other',
            'phone_number' => 'sometimes|required|string|unique:patients,phone_number,' . $patient->id,
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'insurance_details' => 'nullable|array',
        ]);

        $oldValues = $patient->toArray();
        $patient->update($data);

        PatientAudit::create([
            'user_id' => auth()->id(),
            'patient_id' => $patient->id,
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $patient->toArray(),
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'message' => 'Patient updated successfully.',
            'data' => $patient
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);

    } catch (NotFoundHttpException $e) {
        return response()->json([
            'message' => 'Patient not found.'
        ], 404);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while updating the patient.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function destroy($id)
{
    $this->authorizeRole(['Admin']);

    $patient = Patient::where('patient_id', $id)->firstOrFail();

    // Delete related audit records first
    $patient->audits()->delete();

    // Then delete the patient
    $patient->delete();

    return response()->json(['message' => 'Patient deleted']);
}

    protected function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }

    public function audits($id){
       
         $user = Auth::user();

        if (!$user->hasRole('Admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $audits = PatientAudit::where('patient_id', $id)
            ->with('user:id,name,email')
            ->latest()
            ->get();

        return response()->json($audits);
    }
}
