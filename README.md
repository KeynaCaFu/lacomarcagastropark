# LaComarca GastroPark - Sistema de Gestión

**Sistema integral de gestión para restaurantes y locales gastronómicos**

## Descripción General

LaComarca GastroPark es una plataforma web basada en **Laravel** diseñada para administrar de manera eficiente todos los aspectos de un restaurante o local gastronómico, incluyendo:

-  **Gestión de Usuarios** - Administración de personal con roles diferenciados
- **Gestión de Eventos** - Programación y promoción de eventos especiales
- **Gestión de Productos** - Catálogo completo con galerías de fotos
- **Dashboard Personalizado** - Vistas específicas según el rol del usuario
- **Control de Acceso** - Sistema de autenticación seguro con roles

---

## Inicio Rápido

### Credenciales de Acceso Predeterminadas

####  Administrador

- **Email:** `admin@gmail.com`
- **Contraseña:** `admin`
- **Acceso:** Completo al sistema

#### Gerente de Local
- **Email:** `leskas@gmail.com`
- **Contraseña:** `local`

**Email:** `gerente.leskas@gmail.com`  
**Contraseña:** `local`

**Email:** `gerente.cevichito@gmail.com`  
**Contraseña:** `local`

**Email:** `gerente.donchente@gmail.com`  
**Contraseña:** `local`

**Email:** `gerente.muu@gmail.com`  
**Contraseña:** `local`

- **Acceso:** Productos y dashboard del local

---

## Documentación Completa

Toda la documentación está organizada en la carpeta `/docs`:

### Para Comenzar
1. **[Inicio de Sesión](docs/01-inicio-sesion.md)** - Cómo acceder al sistema

### Para Administradores
2. **[Dashboard del Administrador](docs/02-admin-dashboard.md)** - Visión general de controles
3. **[Gestionar Usuarios](docs/03-gestionar-usuarios.md)** - Crear, editar y eliminar usuarios
4. **[Gestionar Eventos](docs/04-gestionar-eventos.md)** - Crear y promocionar eventos

### Para Gerentes de Local
5. **[Dashboard del Gerente](docs/05-gerente-dashboard.md)** - Visión del local
6. **[Gestionar Productos](docs/06-gestionar-productos.md)** - Catálogo completo con fotos

---

## Funcionalidades Principales

### Gestión de Usuarios (Admin)

**Crear nuevos usuarios** con diferentes roles  
**Editar información** de usuarios existentes  
**Eliminar usuarios** de forma segura  
**Cambiar estado** de usuarios (Activo/Inactivo)  
**Asignar roles** con permisos específicos  

###  Gestión de Eventos (Admin)

**Crear eventos especiales** con imagen, fecha y descripción  
**Editar eventos** en cualquier momento  
**Subir fotos** del evento  
**Cambiar estado** (Activo/Inactivo)  
**Eliminar eventos** de forma segura  
**Filtrar por fecha** o nombre  

###  Gestión de Productos (Gerente)

**Registrar productos** con detalles completos  
**Editar información** de precios y descripciones  
**Gestionar galería de fotos** (múltiples imágenes)  
**Establecer foto principal** para cada producto  
**Cambiar estado** (Disponible/No disponible)  
**Categorizar productos** automáticamente  
**Filtrar y buscar** rápidamente  

###  Dashboard Personalizado

**Estadísticas en tiempo real** según el rol  
**Enlaces rápidos** a funciones principales  
**Vista general del local**  
**Información de inventario**  

---

## Stack Tecnológico

- **Backend:** Laravel 11+
- **Frontend:** Blade, Bootstrap 5.1.3, TailwindCSS
- **Base de Datos:** MySQL
- **JavaScript:** Vanilla JS, SweetAlert2
- **Iconos:** Font Awesome 6.0.0

---

## Requisitos del Sistema

- PHP 8.1 o superior
- MySQL 5.7 o superior
- Composer
- Node.js (para compilar assets)

---

## Instalación

### 1. Clonar el Repositorio
```bash
git clone <url-del-repositorio>
cd lacomarcagastropark
```

### 2. Instalar Dependencias
```bash
composer install
npm install
```

### 3. Configurar Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar Base de Datos
```bash
# Editar .env con credenciales MySQL
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=lacogswb_lacomarca
DB_USERNAME=lacogswb_lacomarca
DB_PASSWORD=702810812-402480420
```

### 5. Ejecutar Migraciones
```bash
php artisan migrate --seed
```

### 6. Compilar Assets
```bash
npm run dev
```

### 7. Iniciar Servidor
```bash
php artisan serve
```

Accede a ` https://lacomarcagastropark.com/login`

---

## Estructura del Proyecto

```
lacomarcagastropark/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Helpers/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   ├── js/
│   └── images/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── events/
│   │   ├── products/
│   │   └── users/
│   ├── js/
│   └── css/
├── routes/
├── docs/          # Documentación
├── storage/
└── tests/
```

---

## Seguridad

- Autenticación con Laravel Sanctum
- Validación de datos en frontend y backend
- Protección CSRF
- Encriptación de contraseñas
- Control de acceso basado en roles (RBAC)

---

## 🎓 Guías de Uso

Para más información detallada sobre cómo usar cada módulo, consulta los siguientes documentos:

- **[Guía de Inicio de Sesión](docs/01-inicio-sesion.md)** - Acceso al sistema
- **[Guía del Admin - Dashboard](docs/02-admin-dashboard.md)** - Panel de control
- **[Guía del Admin - Usuarios](docs/03-gestionar-usuarios.md)** - Crear y editar usuarios
- **[Guía del Admin - Eventos](docs/04-gestionar-eventos.md)** - Crear y promocionar eventos
- **[Guía del Gerente - Dashboard](docs/05-gerente-dashboard.md)** - Panel del gerente
- **[Guía del Gerente - Productos](docs/06-gestionar-productos.md)** - Gestión completa de productos

---

##  Solución de Problemas

### El sistema no carga correctamente
1. Verifica que el servidor esté corriendo: `php artisan serve`
2. Limpia caché: `php artisan cache:clear`
3. Recompila assets: `npm run dev`

### No puedo iniciar sesión
1. Verifica las credenciales (mayúsculas/minúsculas)
2. Ejecuta los seeders: `php artisan migrate --seed`
3. Verifica que la base de datos esté activa

### Las fotos no se suben
1. Verifica permisos de la carpeta `storage/app/` (777)
2. Verifica el tamaño máximo (2MB)
3. Verifica formato (JPG, PNG)

### Base de datos no funciona
1. Verifica conexión MySQL
2. Verifica credenciales en `.env`
3. Ejecuta: `php artisan migrate --fresh --seed`

---

##  Roles y Permisos

### Administrador
| Función                | Permiso |
|------------------------|---------|
| Gestionar Usuarios     |    SI   |
| Gestionar Eventos      |    SI   |
| Gestionar Productos    |    SI   |
| Ver Dashboard Global   |    SI   |
| Cambiar Configuración  |    SI   |

### Gerente de Local
|         Función       | Permiso |
|-----------------------|---------|
| Gestionar Usuarios    |    NO   |
| Gestionar Eventos     |    VER  |
| Gestionar Productos   |    SI   |
| Ver Dashboard Local   |    SI   |
| Cambiar Configuración |    NO   |

---

##  Notas de Desarrollo

- Patrón MVC implementado en Laravel
- Validación automática con Form Requests
- Modelos Eloquent con relaciones
- API RESTful para modales AJAX
- Frontend moderno con Bootstrap 5 y Tailwind

---

## Licencia

Este proyecto es propietario de LaComarca GastroPark.

---

##  ¡Bienvenido a LaComarca!

Gracias por usar nuestro sistema de gestión. Para comenzar, accede a la plataforma con tus credenciales y sigue las guías específicas para tu rol.

**Inicio de sesión:** [docs/01-inicio-sesion.md](docs/01-inicio-sesion.md)
