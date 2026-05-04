# ⚡ COMANDOS RÁPIDOS - COPY & PASTE

Copia y pega estos comandos en la terminal (cmd o PowerShell).

---

## 🧪 EJECUTAR PRUEBAS

### Todas las pruebas (Recomendado)
```bash
php artisan test --verbose
```

### Solo Unitarias
```bash
php artisan test --testsuite=Unit --verbose
```

### Solo Integración
```bash
php artisan test --testsuite=Feature --verbose
```

### Solo Flujo Completo
```bash
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php --verbose
```

### Con Cobertura de Código
```bash
php artisan test --coverage
```

### Más Rápido (Paralelo)
```bash
php artisan test --parallel
```

---

## 📊 RENDIMIENTO (JMeter)

### Instalar JMeter
1. Descarga: https://jmeter.apache.org/download_jmeter.cgi
2. Descomprime a `C:\jmeter`

### Verificar Instalación
```bash
jmeter --version
```

### Ejecutar Test (GUI)
```bash
jmeter -t tests/jmeter/LocalRegistrationLoadTest.jmx
```

### Ejecutar Test (CLI - Más Eficiente)
```bash
jmeter -n ^
  -t tests/jmeter/LocalRegistrationLoadTest.jmx ^
  -l resultado.jtl ^
  -j jmeter.log ^
  -g resultado-reporte ^
  -e
```

### Generar Reporte HTML
```bash
jmeter -g resultado.jtl -o reporte-html
```

---

## 🔒 SEGURIDAD (Postman o Browser)

### Opción 1: Descargar Postman
https://www.postman.com/downloads/

### Opción 2: Usar Browser (F12)
1. Abre DevTools (F12)
2. Ve a Network
3. Haz requests
4. Inspecciona respuestas

---

## 🛠️ CONFIGURACIÓN INICIAL

### Instalar Dependencias
```bash
composer install
```

### Crear .env (Si no existe)
```bash
cp .env.example .env
```

### Generar Key
```bash
php artisan key:generate
```

### Crear BD de Testing (MySQL)
```bash
mysql -u root -p
CREATE DATABASE lacomarca_testing;
EXIT;
```

### O Usar SQLite (Más Rápido)
En `phpunit.xml`, descomenta:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Iniciar Servidor Laravel
```bash
php artisan serve
```

---

## 📸 CAPTURAR EVIDENCIAS

### Windows 10/11 - Captura de Pantalla
```bash
# Presiona estas teclas:
Win + Shift + S
# Selecciona área y se copia automáticamente

# Luego pega en Paint o Word
Ctrl + V
```

### Línea de Comandos - Guardar Output
```bash
# Unitarias
php artisan test --testsuite=Unit --verbose > resultado-unitarias.txt

# Integración
php artisan test --testsuite=Feature --verbose > resultado-integracion.txt

# Sistema
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php --verbose > resultado-sistema.txt

# Luego abre y captura las imágenes
```

---

## 🎯 FLUJO COMPLETO (Paso a Paso)

### Paso 1: Verificar Todo Está Instalado
```bash
php --version
composer --version
jmeter --version
mysql --version
```

### Paso 2: Ejecutar Pruebas Funcionales
```bash
php artisan test --verbose
# Esperado: 17 tests passed ✓
```

### Paso 3: Capturar Evidencia
```bash
# Toma screenshot de resultado
# Guarda en carpeta: evidencias/
```

### Paso 4: Ejecutar JMeter
```bash
php artisan serve
# En otra terminal:
jmeter -t tests/jmeter/LocalRegistrationLoadTest.jmx
```

### Paso 5: Pruebas de Seguridad (Postman)
```bash
# Abre Postman o DevTools (F12)
# Sigue pasos en PRUEBAS_SEGURIDAD.md
```

### Paso 6: Documentar Resultados
```bash
# Edita REPORTE_FINAL_PRUEBAS.md
# Con todos los resultados y evidencias
```

---

## 📋 ARCHIVOS IMPORTANTES

### Ver Contenido
```bash
# Ver guía principal
type GUIA_PRUEBAS_COMPLETA.md

# Ver índice
type INDICE_PRUEBAS.md

# Ver ejecución rápida
type EJECUTAR_PRUEBAS.md
```

### Abrir con Editor
```bash
# Abre en VS Code
code GUIA_PRUEBAS_COMPLETA.md

# O en cualquier editor
notepad GUIA_PRUEBAS_COMPLETA.md
```

---

## 🐛 TROUBLESHOOTING RÁPIDO

### Error: "Connection refused"
```bash
# Verifica servidor Laravel corre
php artisan serve
```

### Error: "Table doesn't exist"
```bash
# Crea BD de testing
mysql -u root -p
CREATE DATABASE lacomarca_testing;
```

### Error: "CSRF token mismatch"
```bash
# Normal en API. Usa Authorization header en lugar de CSRF
# En Postman: Authorization → Bearer Token
```

### Error: "No connection could be made"
```bash
# Verifica MySQL está corriendo
# Windows: XAMPP Control Panel → Start MySQL
```

### Tests lentos
```bash
# Usa SQLite en memoria (más rápido)
# En phpunit.xml descomenta sqlite
```

---

## ✅ VERIFICACIÓN FINAL

### Checklist de Archivos
```bash
dir /s tests\Unit\*.php
dir /s tests\Feature\*.php
dir tests\jmeter\*.jmx
dir *.md | findstr "PRUEBA INDICE GUIA"
```

### Checklist de Tests
```bash
php artisan test --testsuite=Unit
# Esperado: 7 passed ✓

php artisan test --testsuite=Feature
# Esperado: 10 passed ✓

php artisan test
# Esperado: 17 passed ✓
```

### Checklist de Documentación
```bash
type INDICE_PRUEBAS.md
type GUIA_PRUEBAS_COMPLETA.md
type EJECUTAR_PRUEBAS.md
type PRUEBAS_RENDIMIENTO.md
type PRUEBAS_SEGURIDAD.md
```

---

## 📞 PREGUNTAS FRECUENTES - LÍNEA DE COMANDOS

**P: ¿Cómo limpio las pruebas anteriores?**
```bash
php artisan test --parallel --recreate-databases
```

**P: ¿Cómo veo qué tests tengo?**
```bash
php artisan test --list
```

**P: ¿Cómo obtengo más detalles de errores?**
```bash
php artisan test --verbose --debug
```

**P: ¿Cómo corro un test específico?**
```bash
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php::test_complete_workflow_register_manager_and_local
```

**P: ¿Cómo genero reporte HTML de cobertura?**
```bash
php artisan test --coverage-html=coverage
# Luego abre: coverage/index.html
```

---

## 🚀 TODO EN UN COMANDO

Para hacerlo TODO (si todo está instalado):

```bash
# 1. Pruebas funcionales
php artisan test --verbose

# 2. Captura evidencia
# (Manual: toma screenshot)

# 3. JMeter
jmeter -n -t tests/jmeter/LocalRegistrationLoadTest.jmx -l resultado.jtl -j jmeter.log

# 4. Pruebas de seguridad
# (Manual: sigue PRUEBAS_SEGURIDAD.md)

# Hecho ✓
```

---

## 💾 GUARDAR RESULTADOS

### Crear Carpeta de Evidencias
```bash
mkdir evidencias
mkdir evidencias\unitarias
mkdir evidencias\integracion
mkdir evidencias\sistema
mkdir evidencias\rendimiento
mkdir evidencias\seguridad
```

### Copiar Resultados
```bash
# JMeter
copy resultado.jtl evidencias\rendimiento\
copy resultado-reporte evidencias\rendimiento\

# Screenshots (manual)
# Copia .jpg/.png a carpetas correspondientes
```

### Generar Reporte Final
```bash
# Abre Word o Google Docs
# Copia y pega:
#   - Screenshots
#   - Resultados de tests
#   - Análisis de seguridad
#   - Conclusiones

# Exporta como PDF
```

---

## 📧 ÚLTIMA VERIFICACIÓN

Antes de presentar, ejecuta esto:

```bash
@echo off
echo === VERIFICACION FINAL ===

echo.
echo [1] Pruebas Unitarias
php artisan test --testsuite=Unit --verbose

echo.
echo [2] Pruebas Integración
php artisan test --testsuite=Feature --verbose

echo.
echo [3] Documentación
dir *.md

echo.
echo === LISTO PARA PRESENTAR ===
```

---

**¡Ya estás listo! Copia y pega los comandos según lo necesites. 🚀**
