<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller {
    public function index() {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('admin.role', compact('users', 'roles'));
    }

    public function assign(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->syncRoles($request->roles); // Expecting array
        return redirect()->back()->with('success', 'Roles updated');
    }
}
