<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('role');
        
        // Búsqueda
        if ($request->has('q') && $request->get('q') !== '') {
            $search = $request->get('q');
            $query->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        // Filtro por rol
        if ($request->has('role') && $request->get('role') !== '') {
            $query->where('role_id', $request->get('role'));
        }

        // Filtro por estado
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        // Tamaño de página
        $perPage = (int)$request->get('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 15, 25]) ? $perPage : 5;

        $users = $query->paginate($perPage)->appends($request->query());
        $roles = Role::all();

        // Si es una solicitud AJAX, devolver solo la tabla
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('users.table', compact('users', 'roles'));
        }

        return view('users.index', compact('users', 'roles', 'perPage'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbuser,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:tbrole,role_id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario creado exitosamente', 'user' => $user], 201);
        }

        return redirect()->route('users.index')
                        ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        if (request()->expectsJson()) {
            return response()->json($user);
        }
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbuser,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:tbrole,role_id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Solo actualizar la contraseña si se proporciona
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user]);
        }

        return redirect()->route('users.index')
                        ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevenir que se elimine el usuario actual
        if (auth()->user()->user_id === $user->user_id) {
            return redirect()->route('users.index')
                            ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Show modal for displaying user details (AJAX)
     */
    public function showModal(User $user)
    {
        return view('users.modals.show', compact('user'));
    }

    /**
     * Show modal for editing user (AJAX)
     */
    public function editModal(User $user)
    {
        $roles = Role::all();
        return view('users.modals.edit-modal', compact('user', 'roles'));
    }
}
