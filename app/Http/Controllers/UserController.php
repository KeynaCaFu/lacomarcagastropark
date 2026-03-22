<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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
        $phoneRaw = (string) $request->input('phone', '');
        $phoneDigits = preg_replace('/\D+/', '', $phoneRaw);

        if (strlen($phoneDigits) === 8) {
            $request->merge([
                'phone' => substr($phoneDigits, 0, 4) . '-' . substr($phoneDigits, 4),
            ]);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbuser,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'role_id' => 'required|exists:tbrole,role_id',
            'phone' => ['nullable', 'regex:/^\d{4}-\d{4}$/'],
            'status' => 'required|in:Active,Inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'full_name.required' => 'El campo nombre completo es obligatorio.',
            'full_name.max' => 'El nombre completo no puede superar los 255 caracteres.',
            'email.required' => 'El campo correo electronico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electronico valido.',
            'email.unique' => 'Este correo electronico ya esta registrado.',
            'password.required' => 'El campo contrasena es obligatorio.',
            'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
            'password_confirmation.required' => 'Debes confirmar la contrasena.',
            'role_id.required' => 'El campo rol es obligatorio.',
            'role_id.exists' => 'El rol seleccionado no es valido.',
            'phone.regex' => 'El telefono debe tener el formato 8888-8888.',
            'status.required' => 'El campo estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es valido.',
            'avatar.image' => 'El archivo de avatar debe ser una imagen.',
            'avatar.mimes' => 'El avatar debe ser de tipo: jpeg, png, jpg o gif.',
            'avatar.max' => 'El avatar no puede ser mayor a 2MB.',
        ], [
            'full_name' => 'nombre completo',
            'email' => 'correo electronico',
            'password' => 'contrasena',
            'password_confirmation' => 'confirmacion de contrasena',
            'role_id' => 'rol',
            'phone' => 'telefono',
            'status' => 'estado',
            'avatar' => 'avatar',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Crear directorio si no existe
            $avatarDir = public_path('images/avatars');
            if (!File::isDirectory($avatarDir)) {
                File::makeDirectory($avatarDir, 0755, true, true);
            }

            // Generar nombre único para el avatar
            $filename = 'avatar_' . time() . '_' . rand(1000, 9999) . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move($avatarDir, $filename);
            $validated['avatar'] = 'images/avatars/' . $filename;
        }

        $user = User::create($validated);

        if ($request->expectsJson()) {
            // Nunca devolver la contraseña en respuestas JSON por seguridad
            $userData = $user->toArray();
            unset($userData['password']);
            return response()->json(['message' => 'Usuario creado exitosamente', 'user' => $userData], 201);
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
            // Nunca devolver la contraseña en respuestas JSON por seguridad
            $userData = $user->toArray();
            unset($userData['password']);
            return response()->json($userData);
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
        // Si solo se está actualizando el estado (desde el toggler)
        if ($request->has('status') && !$request->has('full_name')) {
            $validated = $request->validate([
                'status' => 'required|in:Active,Inactive',
            ]);
            $user->update($validated);

            if ($request->expectsJson()) {
                // Nunca devolver la contraseña en respuestas JSON por seguridad
                $userData = $user->fresh()->toArray();
                unset($userData['password']);
                return response()->json(['message' => 'Estado actualizado exitosamente', 'user' => $userData]);
            }

            return redirect()->route('users.index')
                            ->with('success', 'Estado actualizado exitosamente.');
        }

        // Actualización completa del usuario (desde el formulario de edición)
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tbuser,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:tbrole,role_id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Solo actualizar la contraseña si se proporciona
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar) {
                $oldPath = public_path(str_replace('public/', '', $user->avatar));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Crear directorio si no existe
            $avatarDir = public_path('images/avatars');
            if (!File::isDirectory($avatarDir)) {
                File::makeDirectory($avatarDir, 0755, true, true);
            }

            // Generar nombre único para el avatar
            $filename = 'avatar_' . $user->user_id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move($avatarDir, $filename);
            $validated['avatar'] = 'public/images/avatars/' . $filename;
        }

        $user->update($validated);

        if ($request->expectsJson()) {
            // Nunca devolver la contraseña en respuestas JSON por seguridad
            $userData = $user->fresh()->toArray();
            unset($userData['password']);
            return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $userData]);
        }

        return redirect()->route('users.index')
                        ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        // Prevenir que se elimine el usuario actual
        if ($currentUser && $currentUser->user_id === $user->user_id) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'No puedes eliminar tu propia cuenta.'
                ], 422);
            }

            return redirect()->route('users.index')
                            ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        try {
            DB::transaction(function () use ($user) {
                // Evita fallos de FK en relaciones N:M (por ejemplo tbuser_local)
                $user->locals()->detach();
                $user->delete();
            });
        } catch (QueryException $e) {
            $message = 'No se puede eliminar el usuario porque tiene registros relacionados.';

            if (request()->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->route('users.index')->with('error', $message);
        }

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Usuario eliminado exitosamente']);
        }

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
