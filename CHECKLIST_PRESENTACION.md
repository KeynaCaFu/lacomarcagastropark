# ✅ CHECKLIST DE PRUEBAS - PARA PRESENTACIÓN EN CLASE

Usa este checklist para verificar que completaste TODO correctamente.

---

## 📋 FASE 1: PREPARACIÓN (Antes de Clase)

### Configuración Básica
- [ ] PHP version ≥ 8.0
  ```bash
  php --version
  ```
  
- [ ] Composer instalado
  ```bash
  composer --version
  ```

- [ ] Base de datos de testing creada
  ```bash
  # Opción 1: MySQL (requiere BD real)
  mysql -u root -p
  CREATE DATABASE lacomarca_testing;
  
  # Opción 2: SQLite (más rápido, sin BD)
  # En phpunit.xml descomenta sqlite
  ```

- [ ] Proyecto Laravel configurado
  ```bash
  cd C:\LACOMARCA
  composer install
  ```

### Archivos de Prueba Creados
- [ ] `tests/Unit/UserModelTest.php` existe
- [ ] `tests/Unit/LocalModelTest.php` existe
- [ ] `tests/Feature/UserRegistrationTest.php` existe
- [ ] `tests/Feature/LocalRegistrationTest.php` existe
- [ ] `tests/Feature/LocalRegistrationWorkflowTest.php` existe

### Documentación Creada
- [ ] `INDICE_PRUEBAS.md` existe
- [ ] `GUIA_PRUEBAS_COMPLETA.md` existe
- [ ] `EJECUTAR_PRUEBAS.md` existe
- [ ] `PRUEBAS_RENDIMIENTO.md` existe
- [ ] `PRUEBAS_SEGURIDAD.md` existe

---

## 🧪 FASE 2: PRUEBAS UNITARIAS

### Ejecución
```bash
php artisan test --testsuite=Unit --verbose
```

- [ ] Terminal muestra: `PASS  Tests\Unit\UserModelTest`
- [ ] Terminal muestra: `✓ test_user_can_be_instantiated`
- [ ] Terminal muestra: `✓ test_user_has_correct_fillable_attributes`
- [ ] Terminal muestra: `✓ test_user_uses_correct_table_and_primary_key`
- [ ] Terminal muestra: `PASS  Tests\Unit\LocalModelTest`
- [ ] Terminal muestra: `✓ test_local_can_be_instantiated`
- [ ] Terminal muestra: `✓ test_local_uses_correct_table_and_primary_key`
- [ ] Terminal muestra: `✓ test_local_has_correct_fillable_attributes`
- [ ] Terminal muestra: `✓ test_local_has_timestamps`
- [ ] Terminal muestra: `Tests: 7 passed`

### Evidencia
- [ ] Screenshot de resultado guardado: `evidencia-unitarias.jpg`

---

## 🔗 FASE 3: PRUEBAS DE INTEGRACIÓN

### Ejecución
```bash
php artisan test --testsuite=Feature --verbose
```

- [ ] Terminal muestra: `PASS  Tests\Feature\UserRegistrationTest`
- [ ] Terminal muestra: `✓ test_can_create_manager_user_in_database`
- [ ] Terminal muestra: `✓ test_registered_user_exists_in_database`
- [ ] Terminal muestra: `✓ test_multiple_managers_can_be_created`
- [ ] Terminal muestra: `PASS  Tests\Feature\LocalRegistrationTest`
- [ ] Terminal muestra: `✓ test_can_create_local_in_database`
- [ ] Terminal muestra: `✓ test_local_can_have_multiple_managers`
- [ ] Terminal muestra: `✓ test_created_local_exists_in_database`
- [ ] Terminal muestra: `Tests: 6 passed` (solo Feature)

### Evidencia
- [ ] Screenshot de resultado guardado: `evidencia-integracion.jpg`

---

## 🎯 FASE 4: PRUEBAS DE SISTEMA (Flujo Completo)

### Ejecución
```bash
php artisan test tests/Feature/LocalRegistrationWorkflowTest.php --verbose
```

- [ ] Terminal muestra: `PASS  Tests\Feature\LocalRegistrationWorkflowTest`
- [ ] Terminal muestra: `✓ test_complete_workflow_register_manager_and_local`
- [ ] Terminal muestra: `✓ test_local_without_managers_should_be_detected`
- [ ] Terminal muestra: `✓ test_local_can_have_multiple_managers_workflow`
- [ ] Terminal muestra: `✓ test_local_data_integrity_after_manager_assignment`
- [ ] Terminal muestra: `Tests: 4 passed`

### Verificar Flujo
- [ ] Lee el test y verifica que valida:
  - [ ] Usuario Gerente es creado
  - [ ] Usuario tiene rol correcto
  - [ ] Local es creado
  - [ ] Gerente y Local están relacionados
  - [ ] Relación existe en tabla pivot

### Evidencia
- [ ] Screenshot de resultado guardado: `evidencia-sistema.jpg`

---

## 📊 FASE 5: PRUEBAS DE RENDIMIENTO (JMeter)

### Instalación
- [ ] JMeter descargado desde: https://jmeter.apache.org/download_jmeter.cgi
- [ ] JMeter descomprimido en: `C:\jmeter` (o donde prefieras)
- [ ] Verifica instalación:
  ```bash
  jmeter --version
  # Output: jmeter 5.6.3 (o versión similar)
  ```

### Crear Test Plan
- [ ] Archivo `tests/jmeter/LocalRegistrationLoadTest.jmx` existe
- [ ] Test plan contiene:
  - [ ] Thread Group con 150 usuarios
  - [ ] Ramp-up time de 60 segundos
  - [ ] HTTP Request POST /api/users
  - [ ] HTTP Request POST /api/locals
  - [ ] Response Assertion (validar status 200/201)
  - [ ] Summary Report listener
  - [ ] View Results Tree listener

### Ejecutar Prueba
- [ ] Servidor Laravel corriendo: `php artisan serve`
- [ ] Ejecuta JMeter:
  ```bash
  jmeter -n -t tests/jmeter/LocalRegistrationLoadTest.jmx -l resultado.jtl -j jmeter.log
  ```

### Verificar Resultados
- [ ] Archivo `resultado.jtl` fue creado
- [ ] Abre `resultado.jtl` o genera reporte HTML:
  ```bash
  jmeter -g resultado.jtl -o reporte-jmeter
  ```

### Criterios de Aceptación
- [ ] Avg Response Time < 1000 ms (✅)
- [ ] Error Rate < 1% (✅)
- [ ] 95% Response Time < 2000 ms (✅)
- [ ] Total Requests ≈ 300 (150 users × 2 requests)
- [ ] Successful Requests > 99%

### Evidencia
- [ ] Screenshot 1: Summary Report guardado: `evidencia-jmeter-1.jpg`
- [ ] Screenshot 2: Gráfico de resultados guardado: `evidencia-jmeter-2.jpg`
- [ ] Documento `REPORTE_RENDIMIENTO.md` completado

---

## 🔒 FASE 6: PRUEBAS DE SEGURIDAD

### Herramienta: Postman
- [ ] Postman instalado desde: https://www.postman.com/downloads/
- [ ] O usa Browser DevTools (F12)

### Prueba 1: Inyección SQL
- [ ] Intenta POST /api/users con payload: `' OR '1'='1`
- [ ] Resultado: ✅ RECHAZADO (error 400 o 422)
- [ ] Captura screenshot: `evidencia-seg-1-sql.jpg`

### Prueba 2: XSS
- [ ] Intenta POST /api/users con payload: `<script>alert('XSS')</script>`
- [ ] Resultado: ✅ ESCAPADO (se guarda como texto, no ejecutado)
- [ ] Captura screenshot: `evidencia-seg-2-xss.jpg`

### Prueba 3: CSRF
- [ ] Intenta POST /api/users SIN Authorization header
- [ ] Resultado: ✅ RECHAZADO (error 419 Token Expired)
- [ ] Captura screenshot: `evidencia-seg-3-csrf.jpg`

### Prueba 4: Autenticación (Sin Token)
- [ ] Intenta GET /api/locals SIN Authorization
- [ ] Resultado: ✅ RECHAZADO (error 401 Unauthorized)
- [ ] Captura screenshot: `evidencia-seg-4-auth.jpg`

### Prueba 5: Autorización (Rol Incorrecto)
- [ ] Crea usuario Cliente
- [ ] Intenta POST /api/locals como Cliente
- [ ] Resultado: ✅ RECHAZADO (error 403 Forbidden)
- [ ] Captura screenshot: `evidencia-seg-5-authz.jpg`

### Prueba 6: Exposición de Información
- [ ] POST usuario y revisa respuesta JSON
- [ ] Verifica que password NO aparece en respuesta
- [ ] Resultado: ✅ PASSWORD PROTEGIDO (no en respuesta)
- [ ] Captura screenshot: `evidencia-seg-6-info.jpg`

### Documentación
- [ ] Documento `REPORTE_SEGURIDAD_FINAL.md` completado
- [ ] Incluye:
  - [ ] Vector de ataque usado
  - [ ] Herramienta utilizada
  - [ ] Resultado (bloqueado/protegido)
  - [ ] Razón técnica (por qué está protegido)

---

## 📁 FASE 7: ORGANIZACIÓN DE EVIDENCIAS

### Carpeta de Evidencias
- [ ] Crea carpeta: `evidencias-finales/`
- [ ] Contiene:
  ```
  evidencias-finales/
  ├── unitarias/
  │   └── resultado.jpg
  ├── integracion/
  │   └── resultado.jpg
  ├── sistema/
  │   └── resultado.jpg
  ├── rendimiento/
  │   ├── summary-report.jpg
  │   └── graph-results.jpg
  └── seguridad/
      ├── sql-injection.jpg
      ├── xss.jpg
      ├── csrf.jpg
      ├── auth-sin-token.jpg
      ├── auth-rol-incorrecto.jpg
      └── exposicion-info.jpg
  ```

---

## 📝 FASE 8: DOCUMENTACIÓN FINAL

### Reporte Consolidado
- [ ] Crea o actualiza: `REPORTE_FINAL_PRUEBAS.md`
- [ ] Incluye:
  - [ ] Resumen ejecutivo
  - [ ] Resultados de unitarias (7 tests)
  - [ ] Resultados de integración (6 tests)
  - [ ] Resultados de sistema (4 tests)
  - [ ] Resultados de rendimiento (150 usuarios)
  - [ ] Resultados de seguridad (6 vectores)
  - [ ] Total: 23 pruebas, 23 pasadas, 0 fallos ✅

### Conclusiones
- [ ] Escribe conclusión: "La aplicación cumple con todos los requisitos de seguridad, rendimiento y funcionalidad"
- [ ] Escribe recomendaciones para producción

---

## 🎤 FASE 9: PREPARACIÓN PARA CLASE

### Materiales Necesarios
- [ ] Laptop con todo instalado
- [ ] Archivos descargados/creados
- [ ] Screenshots en carpeta
- [ ] Reportes en PDF o Markdown

### Ensayo
- [ ] Ensaya la presentación (5-10 minutos)
- [ ] Practiqué ejecutar: `php artisan test --verbose`
- [ ] Practiqué JMeter (si lo vas a demostrar)
- [ ] Practiqué Postman (si lo vas a demostrar)

### Documento de Presentación
- [ ] Crea: `PRESENTACION_CLASE.md` con:
  - [ ] Qué se probó
  - [ ] Cómo se probó
  - [ ] Resultados obtenidos
  - [ ] Conclusiones
  - [ ] Link a todas las evidencias

---

## ✅ LISTA FINAL - DÍA DE PRESENTACIÓN

### Estructura de Archivos Verificada
```bash
cd C:\LACOMARCA

# Verificar que existen TODOS los archivos
ls tests/Unit/*.php                     # ✓ 2 archivos
ls tests/Feature/*.php                  # ✓ 3 archivos
ls tests/jmeter/*.jmx                   # ✓ 1 archivo
ls *.md | grep -i prueba                # ✓ 5 archivos
```

- [ ] Todos los archivos existen

### Tests Ejecutados Exitosamente
```bash
# Ejecutar TODO
php artisan test --verbose

# Esperado:
# - 7 tests unitarios ✓
# - 6 tests integración ✓
# - 4 tests sistema ✓
# Total: 17 tests pasados ✓
```

- [ ] Todos los tests pasan

### Evidencias Completas
- [ ] Screenshots de unitarias ✓
- [ ] Screenshots de integración ✓
- [ ] Screenshots de sistema ✓
- [ ] Screenshots de rendimiento (JMeter) ✓
- [ ] Screenshots de seguridad (6 pruebas) ✓

### Documentación Completa
- [ ] GUIA_PRUEBAS_COMPLETA.md ✓
- [ ] EJECUTAR_PRUEBAS.md ✓
- [ ] PRUEBAS_RENDIMIENTO.md ✓
- [ ] PRUEBAS_SEGURIDAD.md ✓
- [ ] REPORTE_RENDIMIENTO.md ✓
- [ ] REPORTE_SEGURIDAD_FINAL.md ✓

### Requisitos Académicos Cumplidos
- [ ] 3+ unitarios por estudiante (7 ✓)
- [ ] 3+ integración (6 ✓)
- [ ] 1+ sistema (4 ✓)
- [ ] Documentación detallada ✓
- [ ] Evidencias técnicas ✓
- [ ] Ejecución en clase ✓

---

## 🚀 ¡LISTO PARA PRESENTAR!

Cuando llegues a clase:

1. **Abre terminal**:
   ```bash
   php artisan serve
   ```

2. **En otra terminal**:
   ```bash
   php artisan test --verbose
   ```

3. **Muestra los resultados**:
   - 17 tests pasados
   - 100% exitoso

4. **Abre documentación**:
   - INDICE_PRUEBAS.md
   - GUIA_PRUEBAS_COMPLETA.md

5. **Presenta evidencias**:
   - Screenshots de cada fase
   - Resultados de rendimiento
   - Análisis de seguridad

---

## 📞 Soporte Último Minuto

Si algo no funciona justo antes de clase:

- [ ] BD de testing existe: `mysql -u root -p; show databases;`
- [ ] Laravel serve corre: `php artisan serve`
- [ ] Tests corren: `php artisan test`
- [ ] Documentación existe: `ls *.md`
- [ ] Evidencias existen: `ls evidencias-finales/`

Si falla algo:
1. Revisa EJECUTAR_PRUEBAS.md → Troubleshooting
2. Reinstala dependencias: `composer install`
3. Limpia cache: `php artisan cache:clear`

---

## ✨ ¡BUENA SUERTE EN CLASE! 🎉

Recuerda:
- Habla con confianza
- Explica qué probaste y por qué
- Muestra las evidencias
- Responde preguntas sobre seguridad y rendimiento

¡Adelante! 🚀
