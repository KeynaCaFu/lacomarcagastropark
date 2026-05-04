# 🎯 ÍNDICE DE PRUEBAS - LA COMARCA

**Hola** 👋 Esta es tu **guía de inicio rápido** para las pruebas técnicas.

---

## ⚡ Inicio Rápido (3 pasos)

### 1️⃣ Leer primero esta guía
```
📖 GUIA_PRUEBAS_COMPLETA.md
```
→ Entenderás TODO el plan, paso a paso

### 2️⃣ Ejecutar las pruebas
```bash
php artisan test --verbose
```
→ Verás 17 tests ejecutándose (unitarias + integración + sistema)

### 3️⃣ Hacer pruebas de rendimiento y seguridad
```
📊 PRUEBAS_RENDIMIENTO.md
🔒 PRUEBAS_SEGURIDAD.md
```
→ Seguir instrucciones en esos archivos

---

## 📚 Estructura de Archivos

```
LACOMARCA/
│
├── 📖 GUIA_PRUEBAS_COMPLETA.md      ← LEE ESTO PRIMERO
├── ⚡ EJECUTAR_PRUEBAS.md            ← Ejecución rápida
├── 📊 PRUEBAS_RENDIMIENTO.md         ← JMeter
├── 🔒 PRUEBAS_SEGURIDAD.md           ← Inyección SQL, XSS, etc.
│
├── tests/
│   ├── Unit/
│   │   ├── UserModelTest.php         ← 3 tests unitarios User
│   │   └── LocalModelTest.php        ← 4 tests unitarios Local
│   │
│   ├── Feature/
│   │   ├── UserRegistrationTest.php  ← 3 tests integración User
│   │   ├── LocalRegistrationTest.php ← 3 tests integración Local
│   │   └── LocalRegistrationWorkflowTest.php ← 4 tests sistema (flujo completo)
│   │
│   └── jmeter/
│       └── LocalRegistrationLoadTest.jmx ← Test plan JMeter (150 usuarios)
│
└── phpunit.xml                        ← Configuración de tests
```

---

## 🎓 Requisitos Académicos - Checklist

### ✅ Pruebas Funcionales
- [ ] **3+ casos unitarios por estudiante**
  - UserModelTest: 3 tests ✅
  - LocalModelTest: 4 tests ✅
  - **Total: 7 tests** ✅

- [ ] **3+ casos de integración**
  - UserRegistrationTest: 3 tests ✅
  - LocalRegistrationTest: 3 tests ✅
  - **Total: 6 tests** ✅

- [ ] **1+ caso de sistema (flujo completo)**
  - LocalRegistrationWorkflowTest: 4 tests ✅
  - **Total: 4 tests** ✅

- [ ] **Documentación detallada**
  - GUIA_PRUEBAS_COMPLETA.md ✅
  - EJECUTAR_PRUEBAS.md ✅
  - Comentarios en cada test ✅

- [ ] **Evidencias técnicas**
  - Screenshots de consola ✅
  - Documentación de resultados ✅

- [ ] **Ejecución en clase**
  - Comando: `php artisan test --verbose` ✅

### ✅ Pruebas de Rendimiento
- [ ] **150 usuarios concurrentes**
  - Configurado en JMeter ✅
- [ ] **Casos obligatorios**:
  - Formulario (registrar usuario) ✅
  - Consulta/Vista (GET locales) ✅
  - Upload (opcional) 
- [ ] **Reportes**:
  - Árbol de resultados ✅
  - Tabla agregada ✅
  - Aserción < 1% errores ✅

### ✅ Pruebas de Seguridad
- [ ] **Inyección SQL** → PRUEBAS_SEGURIDAD.md ✅
- [ ] **XSS** → PRUEBAS_SEGURIDAD.md ✅
- [ ] **CSRF** → PRUEBAS_SEGURIDAD.md ✅
- [ ] **Autenticación/Autorización** → PRUEBAS_SEGURIDAD.md ✅
- [ ] **Exposición de Información** → PRUEBAS_SEGURIDAD.md ✅
- [ ] **Documentación con evidencia** → PRUEBAS_SEGURIDAD.md ✅

---

## 🚀 Ejecución Paso a Paso

### Día 1: Pruebas Unitarias e Integración

```bash
# Terminal, en el proyecto
cd C:\LACOMARCA

# Ejecutar TODAS las pruebas (unitarias + integración + sistema)
php artisan test --verbose

# Esperado: 17 tests pasados ✅
```

**Tiempo**: ~30 segundos

---

### Día 2: Pruebas de Sistema (Flujo Completo)

```bash
# Ejecutar SOLO el flujo completo
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php --verbose

# Esperado: 4 tests pasados ✅
```

**Tiempo**: ~10 segundos

---

### Día 3: Pruebas de Rendimiento (JMeter)

```bash
# Instalar JMeter (una sola vez)
# Descargar desde: https://jmeter.apache.org/download_jmeter.cgi
# Descomprimir a C:\jmeter

# Ejecutar test
jmeter -t tests/jmeter/LocalRegistrationLoadTest.jmx
```

**Tiempo**: ~2-3 minutos (150 usuarios × 60 segundos ramp-up)

---

### Día 4: Pruebas de Seguridad (Manual)

```bash
# Leer: PRUEBAS_SEGURIDAD.md

# Herramienta: Postman (gratis)
# Descargar desde: https://www.postman.com/downloads/

# O: Usar Browser DevTools (F12)
```

**Tiempo**: ~20 minutos (6 vectores)

---

## 📋 Flujo que se Prueba

```
┌─────────────────────────────────────┐
│  FLUJO TESTEADO: Registrar Local    │
└─────────────────────────────────────┘

Requisito 1: Registrar Usuario (Gerente)
   ✅ Crear usuario en BD
   ✅ Asignar rol Gerente
   ✅ Status = Active

         ↓

Requisito 2: Registrar Local
   ✅ Crear Local en BD
   ✅ Asignar nombre, descripción, contacto

         ↓

Requisito 3: Asociar Gerente a Local
   ✅ Crear relación en tabla pivot (tbuser_local)
   ✅ Verificar ambos lados (Local.users / User.locals)

         ↓

✅ EXITOSO: Local tiene Gerente asignado
```

---

## 🎯 Resultados Esperados

### Pruebas Unitarias
```
PASS  Tests\Unit\UserModelTest (3 tests)
PASS  Tests\Unit\LocalModelTest (4 tests)
──────────────────────────────
Tests: 7 passed ✅
```

### Pruebas de Integración
```
PASS  Tests\Feature\UserRegistrationTest (3 tests)
PASS  Tests\Feature\LocalRegistrationTest (3 tests)
──────────────────────────────
Tests: 6 passed ✅
```

### Pruebas de Sistema
```
PASS  Tests\Feature\LocalRegistrationWorkflowTest (4 tests)
──────────────────────────────
Tests: 4 passed ✅
```

### Pruebas de Rendimiento
```
150 Concurrent Users
─────────────────────
Avg Response Time: 485 ms (✅ < 1000ms)
Error Rate: 0% (✅ < 1%)
Throughput: 5 req/sec (✅ > 1 req/sec)
──────────────────────────────
Result: EXITOSO ✅
```

### Pruebas de Seguridad
```
1. SQL Injection: ✅ BLOQUEADO
2. XSS: ✅ ESCAPADO
3. CSRF: ✅ RECHAZADO
4. Auth (sin token): ✅ RECHAZADO
5. Auth (rol incorrecto): ✅ RECHAZADO
6. Exposición Info: ✅ PROTEGIDO
──────────────────────────────
Total Vulnerabilidades: 0 ✅
```

---

## 📖 Documentos Recomendados por Orden

| # | Documento | Cuando Leer | Duración |
|---|-----------|------------|----------|
| 1 | GUIA_PRUEBAS_COMPLETA.md | Primero, para entender todo | 30 min |
| 2 | EJECUTAR_PRUEBAS.md | Antes de correr comandos | 10 min |
| 3 | tests/*.php | Mientras ejecutas las pruebas | 15 min |
| 4 | PRUEBAS_RENDIMIENTO.md | Antes de JMeter | 20 min |
| 5 | PRUEBAS_SEGURIDAD.md | Antes de pruebas de seguridad | 20 min |

---

## 💡 Comandos Clave

```bash
# Ver TODAS las pruebas
php artisan test --verbose

# Ver SOLO unitarias
php artisan test --testsuite=Unit --verbose

# Ver SOLO integración
php artisan test --testsuite=Feature --verbose

# Ver SOLO flujo completo
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php --verbose

# Con cobertura de código
php artisan test --coverage

# Con estadísticas de tiempo
php artisan test --profile

# En paralelo (más rápido)
php artisan test --parallel

# JMeter (GUI)
jmeter -t tests/jmeter/LocalRegistrationLoadTest.jmx

# JMeter (CLI)
jmeter -n -t tests/jmeter/LocalRegistrationLoadTest.jmx -l resultado.jtl -j jmeter.log
```

---

## ❓ Preguntas Frecuentes

**P: ¿Por dónde empiezo?**
A: 
1. Lee `GUIA_PRUEBAS_COMPLETA.md`
2. Ejecuta `php artisan test --verbose`
3. Sigue los demás documentos en orden

**P: ¿Necesito conectarme a BD real?**
A: Sí, pero los tests limpian automáticamente (RefreshDatabase)

**P: ¿Qué si algo falla?**
A: Revisa `EJECUTAR_PRUEBAS.md` → sección "Troubleshooting"

**P: ¿Cuánto tiempo toma TODO?**
A: ~2-3 horas (incluyendo lectura + ejecución)

**P: ¿Puedo modificar los tests?**
A: Sí, están hechos para adaptarse a tus necesidades

**P: ¿Necesito herramientas costosas?**
A: No, todo es gratis (Laravel, PHPUnit, JMeter, Postman free)

---

## 📞 Soporte

Si algo no funciona:
1. Revisa el archivo `EJECUTAR_PRUEBAS.md` → "Troubleshooting"
2. Verifica que BD de testing existe
3. Verifica que `php artisan serve` está corriendo
4. Revisa logs: `tail -f storage/logs/laravel.log`

---

## ✅ Checklist Antes de Presentar

- [ ] Corrí `php artisan test --verbose` y pasaron TODOS
- [ ] Tengo screenshots de la ejecución
- [ ] Instalé JMeter y creé el test plan
- [ ] Ejecuté JMeter y tengo los resultados
- [ ] Hice las 6 pruebas de seguridad
- [ ] Tengo evidencias (screenshots) de todo
- [ ] Documenté los resultados en Markdown
- [ ] Puedo ejecutar TODO en clase sin internet

---

## 🎉 Listo para Empezar

Lee el siguiente archivo:

→ **[GUIA_PRUEBAS_COMPLETA.md](GUIA_PRUEBAS_COMPLETA.md)**

Luego ejecuta:
```bash
php artisan test --verbose
```

¡Buena suerte! 🚀
