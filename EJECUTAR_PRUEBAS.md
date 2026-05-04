# 🚀 EJECUCIÓN RÁPIDA DE PRUEBAS - LA COMARCA

## ⚡ Ejecutar TODO en 2 minutos

### Opción 1: Ejecutar TODAS las pruebas (Recomendado)

```bash
php artisan test --verbose
```

**Resultado esperado**: 
```
PASS  Tests\Unit\UserModelTest
  ✓ test_user_can_be_instantiated
  ✓ test_user_has_correct_fillable_attributes
  ✓ test_user_uses_correct_table_and_primary_key

PASS  Tests\Unit\LocalModelTest
  ✓ test_local_can_be_instantiated
  ✓ test_local_uses_correct_table_and_primary_key
  ✓ test_local_has_correct_fillable_attributes
  ✓ test_local_has_timestamps

PASS  Tests\Feature\UserRegistrationTest
  ✓ test_can_create_manager_user_in_database
  ✓ test_registered_user_exists_in_database
  ✓ test_multiple_managers_can_be_created

PASS  Tests\Feature\LocalRegistrationTest
  ✓ test_can_create_local_in_database
  ✓ test_local_can_have_multiple_managers
  ✓ test_created_local_exists_in_database

PASS  Tests\Feature\LocalRegistrationWorkflowTest
  ✓ test_complete_workflow_register_manager_and_local
  ✓ test_local_without_managers_should_be_detected
  ✓ test_local_can_have_multiple_managers_workflow
  ✓ test_local_data_integrity_after_manager_assignment

Tests: 18 passed (18 assertions)
```

---

## 📊 Ejecutar por Categoría

### 1️⃣ SOLO Pruebas Unitarias (Unit Tests)

```bash
php artisan test --testsuite=Unit --verbose
```

Valida:
- ✅ Estructura del modelo User
- ✅ Estructura del modelo Local
- ✅ Atributos, tablas, primary keys

**Tiempo**: ~5 segundos

---

### 2️⃣ SOLO Pruebas de Integración (Feature Tests)

```bash
php artisan test --testsuite=Feature --verbose
```

Valida:
- ✅ Crear usuario en BD
- ✅ Crear Local en BD
- ✅ Relaciones entre User y Local

**Tiempo**: ~15 segundos

---

### 3️⃣ SOLO Pruebas de Sistema (Flujo Completo)

```bash
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php --verbose
```

Valida:
- ✅ Registrar Usuario Gerente
- ✅ Registrar Local
- ✅ Asociar Gerente a Local
- ✅ Verificar relación completa

**Tiempo**: ~10 segundos

---

## 🔍 Ejecutar TEST Individual

### Test específico de User Unitaria

```bash
php artisan test tests/Unit/UserModelTest.php::test_user_can_be_instantiated --verbose
```

### Test específico de Integración Local

```bash
php artisan test tests/Feature/LocalRegistrationTest.php::test_can_create_local_in_database --verbose
```

### Test del Flujo Completo

```bash
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php::test_complete_workflow_register_manager_and_local --verbose
```

---

## 🐛 Troubleshooting

### Error: "Call to undefined method isAdminLocal()"

**Solución**: Asegúrate de que el modelo User carga la relación Role:

```php
// En UserRegistrationTest.php, después de create()
$user->load('role');  // ← Agregar esta línea
$this->assertTrue($user->isAdminLocal());
```

### Error: "Table 'lacomarca_testing.tbuser' doesn't exist"

**Solución 1**: Crear BD de testing

```bash
mysql -u root -p
CREATE DATABASE lacomarca_testing;
EXIT;
```

**Solución 2**: Usar SQLite en memoria (más rápido)

En `phpunit.xml`, descomenta:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Error: "No connection could be made because the target machine actively refused it"

**Solución**: Asegúrate que el servidor de BD está corriendo

```bash
# Windows (MySQL en XAMPP)
# Abre Control Panel de XAMPP y inicia MySQL

# Linux/Mac
sudo systemctl start mysql
```

---

## 📋 Checklist - ¿Qué Debo Verificar?

### Antes de Ejecutar Tests

- [ ] Servidor BD está corriendo (MySQL/SQLite)
- [ ] Tabla `tbuser` existe
- [ ] Tabla `tblocal` existe
- [ ] Tabla `tbuser_local` (pivot) existe
- [ ] Tabla `tbrole` existe con al menos rol "Gerente"

### Después de Ejecutar Tests

- [ ] Todos los tests muestran ✓ (verde)
- [ ] No hay errores en output
- [ ] El summary muestra "Tests: XX passed"
- [ ] Cobertura de código visible (si usas --coverage)

---

## 💾 Generar Reporte de Cobertura

```bash
# Genera reporte HTML de qué código está siendo probado
php artisan test --coverage

# O más detallado
php artisan test --coverage --coverage-html=coverage
```

Luego abre `coverage/index.html` en tu navegador.

---

## ⏱️ Benchmark - Tiempo de Ejecución

```bash
# Medir tiempo de ALL tests
php artisan test --profile

# Output:
# Slowest Tests:
#   test_complete_workflow_register_manager_and_local ... 245ms
#   test_multiple_managers_can_be_created .............. 120ms
#   test_local_can_have_multiple_managers ............ 98ms
```

---

## 🔄 Ejecutar en Paralelo (Más Rápido)

```bash
# Ejecutar 4 tests en paralelo (si tienes 4 cores)
php artisan test --parallel --processes=4
```

---

## 📸 Evidencia para Presentación

### Captura 1: Resultado de Unitarias

```bash
php artisan test --testsuite=Unit --verbose > resultado-unitarias.txt
```

### Captura 2: Resultado de Integración

```bash
php artisan test --testsuite=Feature --verbose > resultado-integracion.txt
```

### Captura 3: Resultado de Sistema

```bash
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php --verbose > resultado-sistema.txt
```

Luego:
1. Abre cada `.txt`
2. Toma screenshot
3. Inclúyelos en tu reporte

---

## 🎯 Requisitos de Calificación

Para cumplir con los requisitos académicos:

| Requisito | Status | Evidencia |
|-----------|--------|-----------|
| 3+ casos unitarios por estudiante | ✅ | UserModelTest (3) + LocalModelTest (4) = 7 |
| 3+ casos de integración | ✅ | UserRegistrationTest (3) + LocalRegistrationTest (3) = 6 |
| 1+ caso de sistema | ✅ | LocalRegistrationWorkflowTest (4) = 4 |
| Documentación detallada | ✅ | GUIA_PRUEBAS_COMPLETA.md |
| Evidencias técnicas | 📸 | Screenshots de consola |
| Ejecución en clase | 🔴 | Haz `php artisan test` en clase |

**Total**: 17 pruebas funcionales = ✅ CUMPLE REQUISITO

---

## 🚀 Comando Final para la Clase

```bash
# Ejecuta ESTO en clase y captura el output
php artisan test --verbose 2>&1 | tee resultado-pruebas-final.txt
```

Luego comparte `resultado-pruebas-final.txt` como evidencia.

---

## ❓ FAQ

**P: ¿Por qué RefreshDatabase limpia la BD?**
A: Para cada test, limpia y reconstruye la BD, evitando que un test afecte a otro.

**P: ¿Debo tener datos precargados?**
A: No, cada test crea sus propios datos. Eso es lo bueno.

**P: ¿Puedo modificar los tests?**
A: Sí, están hechos para que los adaptes a tus necesidades específicas.

**P: ¿Cómo valido que mi BD está bien?**
A: Si los tests pasan, tu BD está bien configurada.

