# Sistema de Autenticación - La Comarca Admin

## Descripción General

Se ha implementado un sistema de autenticación robusto usando **Laravel Breeze** con soporte para dos roles:

### Roles Disponibles

1. **Administrador Principal (role_id = 1)**
   - Acceso a todas las funcionalidades del sistema
   - Gestión de Eventos
   - Gestión de Productos
   - Gestión de Proveedores
   - Ruta: `/eventos`

2. **Gerente (role_id = 2)**
   - Acceso limitado a su local asignado
   - Gestión de Insumos (Supplies)
   - Solo puede ver/editar datos de su local
   - Ruta: `/insumos`

## Credenciales de Prueba

```
Administrador Global:
  Email: admin@gmail.com
  Contraseña: password

Gerente Punta Mona (Local: recv):
  Email: gerente.puntamona@gmail.com
  Contraseña: password

Gerente El Sevichito (Local: kcf):
  Email: gerente.sevichito@gmail.com
  Contraseña: password
```

## Estructura de Tablas Utilizadas

### tbuser
- `user_id` (bigint, PK)
- `full_name` (varchar)
- `phone` (varchar)
- `email` (varchar, unique)
- `password` (varchar)
- `role_id` (bigint, FK -> tbrole)
- `status` (enum: 'Active', 'Inactive')
- `created_at`, `updated_at` (timestamps)

### tbrole
- `role_id` (bigint, PK)
- `role_type` (varchar)
- `permissions_list` (text)
- `created_at`, `updated_at` (timestamps)

### tbuser_local (Relación muchos a muchos)
- `user_id` (bigint, FK -> tbuser)
- `local_id` (bigint, FK -> tblocal)
- `created_at`, `updated_at` (timestamps)

## Modelos Utilizados

### User Model (`app/Models/User.php`)
- Tabla: `tbuser`
- Clave primaria: `user_id`
- Métodos útiles:
  - `isAdminGlobal()` - Verifica si es admin global
  - `isAdminLocal()` - Verifica si es gerente
  - `isActive()` - Verifica si está activo
  - `locals()` - Obtiene locales del gerente

### Role Model (`app/Models/Role.php`)
- Tabla: `tbrole`
- Clave primaria: `role_id`

### Local Model (`app/Models/Local.php`)
- Tabla: `tblocal`
- Clave primaria: `local_id`

## Middleware de Autenticación

Se han creado dos middleware personalizados en `app/Http/Middleware/`:

1. **IsAdminGlobal.php**
   - Verifica que el usuario sea administrador principal
   - Uso: `Route::middleware(['auth', 'admin.global'])`

2. **IsAdminLocal.php**
   - Verifica que el usuario sea gerente
   - Uso: `Route::middleware(['auth', 'admin.local'])`

## Vista de Login Personalizada

La vista de login se encuentra en `resources/views/auth/login.blade.php` y contiene:

- Diseño personalizado con estilos de La Comarca
- Información de credenciales de prueba
- Validación de formularios
- Redirección automática según rol

## Flujo de Autenticación

1. Usuario accede a `/login`
2. Completa el formulario con email y contraseña
3. Sistema valida las credenciales en `tbuser`
4. Si es válido, verifica el `role_id`
5. **Si es Administrador (role_id=1)**: Redirecciona a `/eventos`
6. **Si es Gerente (role_id=2)**: Redirecciona a `/insumos`

## Rutas Protegidas

### Admin Global (role_id = 1)
```
/eventos         - Listado de eventos
/productos       - Gestión de productos
/proveedores     - Gestión de proveedores
```

### Admin Local (role_id = 2)
```
/insumos         - Gestión de insumos/supplies
```

### Rutas Públicas
```
/                - Página de bienvenida
/login           - Formulario de login
/register        - Registro (si está habilitado)
/forgot-password - Recuperación de contraseña
```

## Instalación y Configuración

### 1. Modelos y Migraciones
Los modelos ya están creados:
- `app/Models/User.php` (usa tabla `tbuser`)
- `app/Models/Role.php` (usa tabla `tbrole`)
- `app/Models/Local.php` (usa tabla `tblocal`)

### 2. Datos Iniciales
Ejecutar los seeders:
```bash
php artisan db:seed
```

Esto insertará:
- 2 roles (Administrator, Manager)
- 3 usuarios de prueba
- Asignaciones de locales a gerentes

### 3. Verificación
```bash
# Probar que la autenticación funciona
php artisan tinker
>>> App\Models\User::first()
```

## Modificación de Usuarios

Para crear un nuevo usuario:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'full_name' => 'Nombre Completo',
    'email' => 'usuario@ejemplo.com',
    'phone' => '8888-0000',
    'password' => Hash::make('password123'),
    'role_id' => 2, // 1 para admin, 2 para gerente
    'status' => 'Active'
]);

// Si es gerente, asignarle un local:
$user->locals()->attach(1); // local_id = 1
```

## Seguridad

- Las contraseñas se almacenan hasheadas con Bcrypt
- El middleware de autenticación verifica el estado activo del usuario
- Las rutas están protegidas con autenticación y verificación de rol
- Se usa CSRF protection en formularios

## Próximos Pasos

1. Personalizar el dashboard según el rol del usuario
2. Implementar filtrado de datos según local asignado
3. Agregar más funcionalidades de seguridad (2FA, auditoría)
4. Crear panel de administración de usuarios
5. Configurar recuperación de contraseña por email
