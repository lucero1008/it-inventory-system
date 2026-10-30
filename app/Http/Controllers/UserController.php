<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showUser(Request $request) {

        if(!Auth::check()) {
            return Inertia::location('/');
        }

        session(['user_index_url' => $request->fullUrl()]);

        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        $select= $request->input('select');

        $sort = in_array($request->input('sort'), ['id', 'name', 'username', 'status'])
                ? $request->input('sort')
                : 'id';
        $direction = $request->input('direction', 'desc');

        $users = User::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
        })
        ->when($select, function ($query, $select) {
            $query->where('status', $select);
        })
        ->orderBy($sort, $direction)
        ->paginate($perPage)
        ->withQueryString();

        return Inertia::render('Users', [
            'users' => $users,
            'pageTitle' => 'Users',
            'filters' => $request->only(['search', 'perPage', 'sort', 'direction']),
        ]);
    }

    public function action($id = null) {
        $userData = $id ? User::find($id) : null;

        return Inertia::render('Action/ActionUsers', [
            'selectedUser' => $userData,
            'pageTitle' => $id ? 'Edit User' : 'Add User',
            'returnUrl' => session('user_index_url', route('device.index')),
        ]);
    }

    public function store(Request $request) {

        $redirectUrl = session('user_index_url', route('users.index'));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'usertype' => ['required', 'string'],
            'status' => ['required', 'string'],
            'password' => ['required', 'min:6', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[@$!%*#?&]/', 'confirmed'],
        ]);

        $validated['username'] = trim(strtolower($validated['username']));
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
         return redirect($redirectUrl)->with('success', 'User successfully created.');
    }


    public function update(Request $request, User $user) {

        $redirectUrl = session('user_index_url', route('users.index'));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'usertype' => ['required', 'string'],
            'status' => ['required', 'string'],
        ]);

        $data = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'usertype' => $validated['usertype'],
            'status' => $validated['status'],
        ];

        if (!empty($request->password)) {
            $passwordValidated = $request->validate([
                'password' => ['required', 'min:6', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[@$!%*#?&]/', 'confirmed'],
            ]);

            $data['password'] = Hash::make($passwordValidated['password']);
        }

        $user->update($data);

        $hasChanges = $user->wasChanged(['name', 'username', 'usertype', 'status', 'password']);

        $msgType = $hasChanges ? 'success' : 'warning';
        $msgContent = $hasChanges ? 'User details updated successfully.' : 'No changes were made to the user details.';

        return redirect($redirectUrl)->with($msgType, $msgContent);
    }
}
