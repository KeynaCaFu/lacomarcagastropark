{{-- EJEMPLOS DE USO DE GOOGLE AUTH EN BLADE --}}

{{-- ================================================================== --}}
{{-- 1. MOSTRAR INFORMACIÓN DEL USUARIO AUTENTICADO --}}
{{-- ================================================================== --}}

@auth
    {{-- Usuario está autenticado --}}
    <div class="user-info">
        @if(auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->full_name }}" class="user-avatar">
        @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="user-avatar">
        @endif
        
        <h3>{{ auth()->user()->full_name }}</h3>
        <p>{{ auth()->user()->email }}</p>
        
        @if(auth()->user()->provider)
            <span class="badge badge-google">
                <i class="fa-brands fa-{{ auth()->user()->provider }}"></i>
                Autenticado con {{ ucfirst(auth()->user()->provider) }}
            </span>
        @else
            <span class="badge badge-default">Autenticación Local</span>
        @endif
    </div>
@endauth


{{-- ================================================================== --}}
{{-- 2. BOTÓN DE LOGOUT --}}
{{-- ================================================================== --}}

<form action="{{ route('logout') }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit" class="btn btn-logout">
        <i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión
    </button>
</form>


{{-- ================================================================== --}}
{{-- 3. VERIFICAR SI USUARIO ESTÁ AUTENTICADO CON GOOGLE --}}
{{-- ================================================================== --}}

@if(auth()->check() && auth()->user()->provider === 'google')
    <p>Este usuario se autenticó con Google</p>
    {{-- Mostrar opciones específicas para usuarios de Google --}}
@endif


{{-- ================================================================== --}}
{{-- 4. MOSTRAR DIFERENTES CONTENIDO SEGÚN TIPO DE AUTENTICACIÓN --}}
{{-- ================================================================== --}}

@auth
    @if(auth()->user()->isAuthenticatedWithProvider())
        {{-- Usuario autenticado con proveedor (Google, etc.) --}}
        <div class="alert alert-info">
            Sesión iniciada con {{ ucfirst(auth()->user()->provider) }}
        </div>
    @else
        {{-- Usuario autenticado localmente (email/password) --}}
        <div class="alert alert-default">
            Sesión iniciada con email y contraseña
        </div>
    @endif
@endauth


{{-- ================================================================== --}}
{{-- 5. PANEL DE USUARIO CON OPCIONES SEGÚN ROL --}}
{{-- ================================================================== --}}

<div class="user-menu dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        @if(auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->full_name }}" class="avatar-small">
        @endif
        {{ auth()->user()->full_name }}
    </button>
    
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="fa-solid fa-user"></i> Mi Perfil
            </a>
        </li>
        
        @if(auth()->user()->isAdminGlobal())
            <li>
                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-gauge"></i> Panel Admin
                </a>
            </li>
        @endif
        
        <li><hr class="dropdown-divider"></li>
        
        <li>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>
        </li>
    </ul>
</div>


{{-- ================================================================== --}}
{{-- 6. MOSTRAR AVATAR CON FALLBACK --}}
{{-- ================================================================== --}}

<div class="user-avatar-container">
    @forelse([auth()->user()->avatar] as $avatar)
        @if($avatar)
            <img src="{{ $avatar }}" alt="Avatar" class="avatar" />
        @else
            <div class="avatar bg-secondary text-white">
                {{ substr(auth()->user()->full_name, 0, 1) }}
            </div>
        @endif
    @empty
        <div class="avatar bg-gray text-dark">
            <i class="fa-solid fa-user"></i>
        </div>
    @endforelse
</div>


{{-- ================================================================== --}}
{{-- 7. VERIFICAR ESTADO DEL USUARIO --}}
{{-- ================================================================== --}}

@if(auth()->check())
    @if(auth()->user()->status === 'Active')
        <span class="badge badge-success">Activo</span>
    @else
        <span class="badge badge-danger">Inactivo</span>
    @endif
@endif


{{-- ================================================================== --}}
{{-- 8. TABLA DE USUARIOS CON INFORMACIÓN DE GOOGLE AUTH --}}
{{-- ================================================================== --}}

<table class="table">
    <thead>
        <tr>
            <th>Avatar</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Autenticación</th>
            <th>Rol</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $user)
            <tr>
                <td>
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->full_name }}" 
                             class="avatar-small" style="width: 32px; height: 32px; border-radius: 50%;">
                    @else
                        <div class="avatar-placeholder">
                            {{ substr($user->full_name, 0, 1) }}
                        </div>
                    @endif
                </td>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->provider === 'google')
                        <span class="badge bg-danger">
                            <i class="fa-brands fa-google"></i> Google
                        </span>
                    @else
                        <span class="badge bg-secondary">Local</span>
                    @endif
                </td>
                <td>{{ $user->role->role_type ?? 'N/A' }}</td>
                <td>
                    @if($user->status === 'Active')
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-danger">Inactivo</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No hay usuarios</td>
            </tr>
        @endforelse
    </tbody>
</table>


{{-- ================================================================== --}}
{{-- 9. ESTILOS CSS PARA AVATARES --}}
{{-- ================================================================== --}}

<style>
    /* Avatar pequeño para menús */
    .avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 8px;
    }

    /* Avatar mediano */
    .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
    }

    /* Avatar grande */
    .avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ddd;
    }

    /* Placeholder cuando no hay avatar */
    .avatar-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #666;
    }

    /* Contenedor de avatar */
    .user-avatar-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Badge de Google */
    .badge-google {
        background-color: #4285F4;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    /* Menú de usuario */
    .user-menu {
        position: relative;
    }

    .user-menu .btn {
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>


{{-- ================================================================== --}}
{{-- 10. COMPONENTES DE PERFIL --}}
{{-- ================================================================== --}}

<div class="profile-card">
    <div class="profile-header">
        @if(auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->full_name }}" 
                 class="avatar-large">
        @else
            <div class="avatar avatar-large bg-primary text-white">
                {{ substr(auth()->user()->full_name, 0, 1) }}
            </div>
        @endif
    </div>
    
    <div class="profile-body">
        <h2>{{ auth()->user()->full_name }}</h2>
        <p class="email">{{ auth()->user()->email }}</p>
        
        @if(auth()->user()->phone)
            <p class="phone">
                <i class="fa-solid fa-phone"></i> {{ auth()->user()->phone }}
            </p>
        @endif
        
        <p class="role">
            <strong>Rol:</strong> {{ auth()->user()->role->role_type ?? 'N/A' }}
        </p>
        
        @if(auth()->user()->provider)
            <p class="provider">
                <strong>Autenticación:</strong>
                <span class="badge bg-danger">
                    <i class="fa-brands fa-{{ auth()->user()->provider }}"></i>
                    {{ ucfirst(auth()->user()->provider) }}
                </span>
            </p>
        @endif
        
        <p class="status">
            <strong>Estado:</strong>
            @if(auth()->user()->status === 'Active')
                <span class="badge bg-success">Activo</span>
            @else
                <span class="badge bg-danger">Inactivo</span>
            @endif
        </p>
    </div>
    
    <div class="profile-footer">
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Editar Perfil</a>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
        </form>
    </div>
</div>
