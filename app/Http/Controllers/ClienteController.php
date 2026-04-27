<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Mail\TemporaryPasswordMail;
use Carbon\Carbon;

class ClienteController extends Controller
{
    /**
     * Display the client welcome page
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('client.client-welcome', [
            'user' => $user,
        ]);
    }

    /**
     * Show the client profile edit form
     */
    public function editProfile()
    {
        $user = auth()->user();
        
        return view('client.cliente-perfil', [
            'user' => $user,
        ]);
    }

    /**
     * Update the client profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar) {
                $oldPath = public_path(str_replace(url('/'), '', $user->avatar));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // Crear directorio si no existe
            $avatarDir = public_path('images/avatars');
            if (!File::isDirectory($avatarDir)) {
                File::makeDirectory($avatarDir, 0755, true, true);
            }

            // Guardar nuevo avatar
            $filename = 'avatar_' . $user->user_id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move($avatarDir, $filename);
            $validated['avatar'] = 'images/avatars/' . $filename;
        }

        $user->update($validated);

        return redirect()->route('plaza.index')->with('status', 'Perfil actualizado exitosamente.');
    }

    /**
     * Update the client password
     */
    public function updatePassword(Request $request)
    {
        // No permitir cambio de contraseña si es una cuenta de terceros (Google, etc)
        $user = auth()->user();
        if ($user->provider) {
            return redirect()->route('client.profile.edit')->withErrors([
                'password' => 'No puedes cambiar la contraseña de una cuenta vinculada a ' . ucfirst($user->provider) . '.'
            ]);
        }

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('client.profile.edit')->with('status', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Request a temporary password via email
     */
    public function requestTemporaryPassword(Request $request)
    {
        $user = auth()->user();

        // No permitir si es una cuenta de terceros
        if ($user->provider) {
            return redirect()->route('client.profile.edit')->withErrors([
                'temporary' => 'No puedes recuperar contraseña en una cuenta vinculada a ' . ucfirst($user->provider) . '.'
            ]);
        }

        // Generar contraseña temporal (8 caracteres: mayúsculas, minúsculas, números)
        $temporaryPassword = Str::random(4) . rand(1000, 9999) . Str::random(2);
        
        // Guardar la contraseña temporal en la base de datos (con expiración de 24 horas)
        $user->update([
            'temporary_password' => Hash::make($temporaryPassword),
            'temporary_password_expires_at' => now()->addHours(24),
        ]);

        // Enviar el correo con la contraseña temporal
        Mail::to($user->email)->send(new TemporaryPasswordMail($user, $temporaryPassword));

        return back()->with('status', 'Se ha enviado una contraseña temporal a tu correo. Válida por 24 horas.');
    }

    /**
     * Show the form to change temporary password.
     */
    public function showChangeTemporaryPasswordForm()
    {
        $user = auth()->user();

        // Validar que tenga una contraseña temporal activa
        if (!$user->temporary_password || !$user->temporary_password_expires_at || $user->temporary_password_expires_at < now()) {
            return redirect()->route('plaza.index')->withErrors([
                'temporary_password' => 'La contraseña temporal ha expirado o no es válida.'
            ]);
        }

        return view('auth.change-temporary-password');
    }

    /**
     * Update password using temporary password.
     */
    public function updatePasswordWithTemporary(Request $request)
    {
        $user = auth()->user();

        if ($user->provider) {
            return redirect()->route('client.profile.edit')->withErrors([
                'password' => 'No puedes cambiar la contraseña de una cuenta vinculada a ' . ucfirst($user->provider) . '.'
            ]);
        }

        // Validar que tenga una contraseña temporal activa
        if (!$user->temporary_password || !$user->temporary_password_expires_at || $user->temporary_password_expires_at < now()) {
            return back()->withErrors([
                'temporary_password' => 'La contraseña temporal ha expirado. Solicita una nueva.'
            ], 'temporaryPassword');
        }

        $validated = $request->validateWithBag('temporaryPassword', [
            'temporary_password' => ['required', 'string'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Verificar la contraseña temporal
        if (!Hash::check($validated['temporary_password'], $user->temporary_password)) {
            return back()->withErrors([
                'temporary_password' => 'La contraseña temporal es incorrecta.'
            ], 'temporaryPassword');
        }

        // Actualizar contraseña y limpiar la temporal
        $user->update([
            'password' => Hash::make($validated['password']),
            'temporary_password' => null,
            'temporary_password_expires_at' => null,
        ]);

        return redirect()->route('plaza.index')->with('status', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Show order history for authenticated client
     */
    public function showOrderHistory(Request $request)
    {
        $user = auth()->user();
        
        // Obtener órdenes del usuario autenticado, ordenadas del más reciente al más antiguo
        // Excluir órdenes canceladas
        $query = $user->orders()
            ->where('status', '!=', 'Cancelled')
            ->with(['items.product', 'local'])
            ->orderBy('created_at', 'desc');

        // Filtrar por local si se proporciona
        if ($request->has('local_id') && $request->input('local_id')) {
            $query->where('local_id', $request->input('local_id'));
        }

        $orders = $query->paginate(10);

        // Obtener locales donde el usuario ha hecho pedidos (solo órdenes no canceladas)
        $locales = $user->orders()
            ->where('status', '!=', 'Cancelled')
            ->distinct()
            ->pluck('local_id')
            ->mapWithKeys(function ($localId) {
                $local = \App\Models\Local::find($localId);
                return [$localId => $local];
            })
            ->filter();

        // Si es una solicitud AJAX, devolver JSON
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $ordersData = $orders->map(function ($order) {
                // Traducir estados al español
                $statusTranslated = $this->translateStatus($order->status);
                
                return [
                    'id' => $order->order_id,
                    'order_number' => $order->order_number,
                    'status' => $statusTranslated,
                    'status_en' => $order->status,
                    'date' => $order->date ? Carbon::parse($order->date)->format('d \\d\\e M \\d\\e Y') : '-',
                    'time' => $order->time ? Carbon::parse($order->time)->format('H:i') : '-',
                    'local' => [
                        'name' => $order->local->name ?? '-',
                    ],
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->product->name ?? 'Producto no disponible',
                            'photo_url' => $item->product->photo_url ?? null,
                            'customization' => $item->customization,
                            'quantity' => $item->quantity,
                        ];
                    })->toArray(),
                    'additional_notes' => $order->additional_notes,
                    'total_amount' => number_format($order->total_amount, 2, '.', ','),
                    'items_count' => $order->items->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'orders' => $ordersData,
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
                'has_pages' => $orders->hasPages(),
                'has_more_pages' => $orders->hasMorePages(),
            ]);
        }

        return view('client.order-history', [
            'orders' => $orders,
            'locales' => $locales,
            'selectedLocalId' => $request->input('local_id'),
        ]);
    }

    /**
     * Traducir estados al español
     */
    private function translateStatus($status)
    {
        $translations = [
            'Pending' => 'Pendiente',
            'In Progress' => 'En Preparación',
            'Ready' => 'Listo',
            'Delivered' => 'Entregado',
            'Cancelled' => 'Cancelada',
        ];
        
        return $translations[$status] ?? $status;
    }
}
