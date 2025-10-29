<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{    
    public function index()    
    {
        $users = User::paginate(10);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',            
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create($request->all());

        return redirect()->route('user.show', $user)->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('user.show', compact('user')); 
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',            
            'role_id' => 'exists:roles,id',
        ]);

        $data = $request->only(['name']);
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if (auth()->user()->role->name == 'Admin') {
            $data['role_id'] = $request->role_id;
            $data['email'] = $request->email;
        }

        $user->update($data);

        if(auth()->user()->role->name == 'Admin'){;
            return redirect()->route('user.show', $user)->with('success', 'User updated successfully.');  
        }        

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if($user->email === "admin@example.com"){
            return back()->with('error', 'cannot delete the demo admin account');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully.');        
    }
}
