# 🚀 GUÍA DE INSTALACIÓN - LA COMARCA ADMIN

## ⚡ INSTALACIÓN RÁPIDA

### 1. REQUISITOS PREVIOS
- ✅ XAMPP instalado
- ✅ Composer instalado
- ✅ Git instalado

### 2. COMANDOS PASO A PASO

```bash
# Clonar el repositorio
git clone https://github.com/KeynaCaFu/La-comarca-ADMIN.git
cd La-comarca-ADMIN

# Instalar dependencias
composer install

# Generar clave de aplicación
php artisan key:generate

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan config:cache

# Verificar que Laravel funciona
php artisan --version
```

### 3. CONFIGURAR BASE DE DATOS
1. **Iniciar XAMPP:**
   - Abrir Panel de Control XAMPP
   - Iniciar **Apache** y **MySQL**

2. **Crear base de datos:**
   - Ir a: http://localhost/phpmyadmin
   - Crear nueva BD: `bdsage`

### 4. INICIAR APLICACIÓN
```bash
# Iniciar servidor
php artisan serve
```

### 5. ACCEDER A LA APLICACIÓN
- **Principal:** http://localhost:8000
- **Insumos:** http://localhost:8000/insumos  
- **Proveedores:** http://localhost:8000/proveedores

---

## 🔧 SOLUCIÓN DE PROBLEMAS COMUNES

### Error: "Unable to connect"
- ✅ El servidor no está ejecutándose
- 🔧 Ejecutar: `php artisan serve`

### Error: "Connection refused"
- ✅ MySQL no está iniciado
- 🔧 Iniciar MySQL en XAMPP

### Error: "Table doesn't exist"
- ✅ Las tablas ya existen en la BD
- 🔧 No hacer nada, es normal

### Error: "APP_KEY not set"
- ✅ Falta generar la clave
- 🔧 Ejecutar: `php artisan key:generate`

---

## 🌟 FUNCIONALIDADES

### ✅ INSUMOS
- Ver lista completa
- Crear nuevos insumos
- Editar insumos existentes
- Eliminar insumos
- Control de stock (actual/mínimo)
- Fechas de vencimiento
- Estados (Disponible/Agotado/Vencido)

### ✅ PROVEEDORES
- Ver lista completa
- Crear nuevos proveedores
- Editar proveedores
- Eliminar proveedores
- Control de compras totales
- Estados (Activo/Inactivo)

### ✅ RELACIONES
- Proveedores ↔ Insumos (muchos a muchos)
- Gestión completa de relaciones

---

## ✅ EVENTO
- Ver lista completa
- Crear nuevos eventos
- Editar eventos existentes
- Eliminar eventos


