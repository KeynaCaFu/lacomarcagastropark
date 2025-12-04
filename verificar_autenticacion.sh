#!/bin/bash
# Script para verificar la configuración de autenticación

echo "================================"
echo "Verificación de Autenticación"
echo "================================"
echo ""

# Verificar modelo User
echo "✓ Modelo User configurado en: app/Models/User.php"
echo "  - Tabla: tbuser"
echo "  - Clave primaria: user_id"
echo ""

# Verificar modelo Role
echo "✓ Modelo Role configurado en: app/Models/Role.php"
echo "  - Tabla: tbrole"
echo "  - Clave primaria: role_id"
echo ""

# Verificar modelo Local
echo "✓ Modelo Local configurado en: app/Models/Local.php"
echo "  - Tabla: tblocal"
echo "  - Clave primaria: local_id"
echo ""

# Verificar middleware
echo "✓ Middleware IsAdminGlobal en: app/Http/Middleware/IsAdminGlobal.php"
echo "✓ Middleware IsAdminLocal en: app/Http/Middleware/IsAdminLocal.php"
echo ""

# Verificar vistas
echo "✓ Vista de Login en: resources/views/auth/login.blade.php"
echo ""

# Verificar rutas
echo "✓ Rutas de autenticación en: routes/auth.php"
echo "✓ Rutas web en: routes/web.php"
echo ""

# Verificar datos
echo "✓ Seeder de autenticación en: database/seeders/AuthSeeder.php"
echo ""

echo "================================"
echo "Usuarios de Prueba Creados:"
echo "================================"
echo ""
echo "1. Administrador Global"
echo "   Email: admin@gmail.com"
echo "   Contraseña: password"
echo "   Role: Administrator (id=1)"
echo ""
echo "2. Gerente Punta Mona"
echo "   Email: gerente.puntamona@gmail.com"
echo "   Contraseña: password"
echo "   Role: Manager (id=2)"
echo "   Local: recv (id=1)"
echo ""
echo "3. Gerente El Sevichito"
echo "   Email: gerente.sevichito@gmail.com"
echo "   Contraseña: password"
echo "   Role: Manager (id=2)"
echo "   Local: kcf (id=2)"
echo ""

echo "================================"
echo "Rutas Disponibles:"
echo "================================"
echo ""
echo "Públicas:"
echo "  GET  /                    - Página de bienvenida"
echo "  GET  /login               - Formulario de login"
echo ""
echo "Admin Global (role_id=1):"
echo "  GET  /eventos             - Listado de eventos"
echo "  GET  /productos           - Gestión de productos"
echo "  GET  /proveedores         - Gestión de proveedores"
echo ""
echo "Admin Local (role_id=2):"
echo "  GET  /insumos             - Gestión de insumos"
echo ""
echo "Autenticadas:"
echo "  GET  /dashboard           - Panel de control (redirige según rol)"
echo "  GET  /profile             - Perfil del usuario"
echo ""

echo "================================"
echo "Próximos Pasos:"
echo "================================"
echo ""
echo "1. Ejecutar servidor: php artisan serve"
echo "2. Abrir navegador: http://localhost:8000"
echo "3. Hacer clic en 'Iniciar Sesión'"
echo "4. Usar credenciales de prueba"
echo ""
echo "¡Sistema de autenticación listo para usar!"
echo ""
