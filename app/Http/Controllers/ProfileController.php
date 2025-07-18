<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
  public function update(Request $request): RedirectResponse
{
    // Validate input
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->ignore($request->user()->id),
        ],
    ]);

    // Update user
    $user = $request->user();
    $user->name = $request->name;
    $user->email = $request->email;

    if ($user->isDirty('email')) {
        $user->email_verified_at = null; // Force re-verification if email changed
    }

    $user->save();

    // Redirect back with success message
    return redirect()->back()->with('success','Update Successfully');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

        public function editPassword()
{

    return view('profile.password');
}

public function updatePassword(Request $request)
{
    
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if (!\Hash::check($request->current_password, auth()->user()->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    auth()->user()->update(['password' => \Hash::make($request->password)]);

    return back()->with('success', 'Password updated successfully.');
}
}
