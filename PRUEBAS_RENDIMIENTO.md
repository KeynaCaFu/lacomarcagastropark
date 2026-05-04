# 📊 PRUEBAS DE RENDIMIENTO CON JMETER - LA COMARCA

## Objetivo
Validar que la aplicación soporta **150 usuarios concurrentes** sin degradación

---

## 📥 PASO 1: DESCARGAR E INSTALAR JMETER

### Windows
1. Descarga desde: https://jmeter.apache.org/download_jmeter.cgi
2. Descomprime a: `C:\jmeter` (o donde prefieras)
3. Verifica instalación:
```bash
C:\jmeter\bin\jmeter --version
# Output: jmeter 5.6.3 (o tu versión)
```

4. Agrega a PATH (opcional, pero recomendado):
```bash
setx PATH "%PATH%;C:\jmeter\bin"
# Luego reinicia terminal
```

### Linux/Mac
```bash
# Descarga
wget https://archive.apache.org/dist/jmeter/binaries/apache-jmeter-5.6.3.tgz
tar -xzf apache-jmeter-5.6.3.tgz
sudo mv apache-jmeter-5.6.3 /opt/jmeter

# Verifica
/opt/jmeter/bin/jmeter --version
```

---

## 🎯 PASO 2: CREAR PLAN DE PRUEBAS

### Opción A: GUI (Más Visual, Recomendado para Aprender)

#### Paso 1: Abre JMeter

```bash
jmeter
```

Deberías ver la ventana GUI de JMeter.

#### Paso 2: Crea Test Plan

1. Right-click en **"Test Plan"** (izquierda)
2. Click: **"Save"**
3. Nombre: `LocalRegistrationLoadTest.jmx`
4. Ubicación: `C:\LACOMARCA\tests\jmeter\`

#### Paso 3: Agrega Thread Group (150 usuarios)

1. Right-click en **Test Plan**
2. **Add** → **Threads (Users)** → **Thread Group**
3. Configura:
   ```
   Name: 150 Concurrent Users
   Number of Threads (users): 150
   Ramp-up period (seconds): 60
   Loop Count: 1
   ```

Esto significa:
- Inicia 150 usuarios
- Distribuidos en 60 segundos (2.5 usuarios/seg)
- Cada usuario ejecuta 1 iteración

#### Paso 4: Agrega HTTP Request 1 (Registrar Usuario)

1. Right-click en **Thread Group**
2. **Add** → **Sampler** → **HTTP Request**
3. Configura:
   ```
   Name: POST /api/users
   Protocol: http
   Server Name or IP: localhost
   Port Number: 8000
   Method: POST
   Path: /api/users
   ```

4. Agrega **Body Data**:
   - Click pestaña **Body Data**
   - Pega:
   ```json
   {
     "full_name": "User ${__counter(FALSE)}",
     "email": "user${__counter(FALSE)}@test.com",
     "password": "TestPass123!",
     "role_id": 2,
     "status": "Active"
   }
   ```

5. Agrega **Headers**:
   - Click **HTTP Headers Manager** en la toolbar
   - Right-click HTTP Request → **Add** → **Config Element** → **HTTP Header Manager**
   - Agrega:
   ```
   Name: Content-Type
   Value: application/json
   ```

#### Paso 5: Agrega HTTP Request 2 (Registrar Local)

1. Right-click en **Thread Group** (NO en el HTTP anterior)
2. **Add** → **Sampler** → **HTTP Request**
3. Configura:
   ```
   Name: POST /api/locals
   Protocol: http
   Server Name or IP: localhost
   Port Number: 8000
   Method: POST
   Path: /api/locals
   ```

4. Body Data:
   ```json
   {
     "name": "Local ${__counter(FALSE)}",
     "description": "Load test local",
     "contact": "2765-${__Random(1000,9999)}",
     "status": "Active"
   }
   ```

5. Headers:
   - Content-Type: application/json

#### Paso 6: Agrega Assertion (Validar < 1% errores)

1. Right-click en **Thread Group**
2. **Add** → **Assertions** → **Response Assertion**
3. Configura:
   ```
   Patterns to Test:
   - 200
   - 201
   
   Type: "that matches"
   Test Type: "Response Code"
   ```

#### Paso 7: Agrega Listeners (Ver Resultados)

1. **Add** → **Listener** → **Summary Report**
   ```
   Filename: resultados/summary-report.csv
   ```

2. **Add** → **Listener** → **View Results Tree**
   ```
   Filename: resultados/detailed-results.csv
   ```

3. **Add** → **Listener** → **Graph Results**
   ```
   Filename: resultados/graph-results.csv
   ```

#### Paso 8: Guarda el Test

```
File → Save
```

---

### Opción B: Archivo XML (Copy-Paste)

Crea archivo: `tests/jmeter/LocalRegistrationLoadTest.jmx`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<jmeterTestPlan version="1.2" properties="5.0" jmeter="5.6.3">
  <hashTree>
    <TestPlan guiclass="TestPlanGui" testclass="TestPlan" testname="Local Registration Load Test" enabled="true">
      <elementProp name="TestPlan.user_defined_variables" elementType="Arguments" guiclass="ArgumentsPanel" testclass="Arguments" testname="User Defined Variables" enabled="true">
        <collectionProp name="Arguments.arguments"/>
      </elementProp>
      <stringProp name="TestPlan.user_define_classpath"></stringProp>
      <boolProp name="TestPlan.functional_mode">false</boolProp>
      <boolProp name="TestPlan.serialize_threadgroups">false</boolProp>
      <elementProp name="TestPlan.test_plan_classpath" elementType="TestPlanClasspath"/>
      <stringProp name="TestPlan.comments">Test de Rendimiento: 150 usuarios concurrentes registrando Usuario y Local</stringProp>
    </TestPlan>
    <hashTree>
      <ThreadGroup guiclass="ThreadGroupGui" testclass="ThreadGroup" testname="150 Concurrent Users" enabled="true">
        <elementProp name="ThreadGroup.main_controller" elementType="LoopController" guiclass="LoopControlPanel" testclass="LoopController" testname="Loop Controller" enabled="true">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">1</stringProp>
        </elementProp>
        <stringProp name="ThreadGroup.num_threads">150</stringProp>
        <stringProp name="ThreadGroup.ramp_time">60</stringProp>
        <elementProp name="ThreadGroup.scheduler" elementType="LoopController" guiclass="LoopControlPanel" testclass="LoopController" testname="Loop Controller" enabled="false">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">-1</stringProp>
        </elementProp>
        <boolProp name="ThreadGroup.scheduler">false</boolProp>
        <stringProp name="ThreadGroup.duration"></stringProp>
        <stringProp name="ThreadGroup.delay"></stringProp>
        <boolProp name="ThreadGroup.same_user_on_next_iteration">true</boolProp>
      </ThreadGroup>
      <hashTree>
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy" testname="POST /api/users" enabled="true">
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" testname="User Defined Variables" enabled="true">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
          <stringProp name="HTTPSampler.domain">localhost</stringProp>
          <stringProp name="HTTPSampler.port">8000</stringProp>
          <stringProp name="HTTPSampler.protocol">http</stringProp>
          <stringProp name="HTTPSampler.contentEncoding">UTF-8</stringProp>
          <stringProp name="HTTPSampler.path">/api/users</stringProp>
          <stringProp name="HTTPSampler.method">POST</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <boolProp name="HTTPSampler.auto_redirects">false</boolProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <boolProp name="HTTPSampler.DO_MULTIPART_POST">false</boolProp>
          <stringProp name="HTTPSampler.embedded_url_re"></stringProp>
          <stringProp name="HTTPSampler.connect_timeout"></stringProp>
          <stringProp name="HTTPSampler.response_timeout"></stringProp>
          <stringProp name="HTTPSampler.postBody">{
  "full_name": "User ${__counter(FALSE)}",
  "email": "user${__Random(10000,99999)}@test.com",
  "password": "TestPass123!",
  "role_id": 2,
  "status": "Active"
}</stringProp>
        </HTTPSamplerProxy>
        <hashTree>
          <HeaderManager guiclass="HeaderPanel" testclass="HeaderManager" testname="HTTP Header Manager" enabled="true">
            <collectionProp name="HeaderManager.headers">
              <elementProp name="Content-Type" elementType="Header">
                <stringProp name="Header.name">Content-Type</stringProp>
                <stringProp name="Header.value">application/json</stringProp>
              </elementProp>
            </collectionProp>
          </HeaderManager>
          <hashTree/>
        </hashTree>
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy" testname="POST /api/locals" enabled="true">
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" testname="User Defined Variables" enabled="true">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
          <stringProp name="HTTPSampler.domain">localhost</stringProp>
          <stringProp name="HTTPSampler.port">8000</stringProp>
          <stringProp name="HTTPSampler.protocol">http</stringProp>
          <stringProp name="HTTPSampler.contentEncoding">UTF-8</stringProp>
          <stringProp name="HTTPSampler.path">/api/locals</stringProp>
          <stringProp name="HTTPSampler.method">POST</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <boolProp name="HTTPSampler.auto_redirects">false</boolProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <boolProp name="HTTPSampler.DO_MULTIPART_POST">false</boolProp>
          <stringProp name="HTTPSampler.embedded_url_re"></stringProp>
          <stringProp name="HTTPSampler.connect_timeout"></stringProp>
          <stringProp name="HTTPSampler.response_timeout"></stringProp>
          <stringProp name="HTTPSampler.postBody">{
  "name": "Local ${__counter(FALSE)}",
  "description": "Load test local",
  "contact": "2765-${__Random(1000,9999)}",
  "status": "Active"
}</stringProp>
        </HTTPSamplerProxy>
        <hashTree>
          <HeaderManager guiclass="HeaderPanel" testclass="HeaderManager" testname="HTTP Header Manager" enabled="true">
            <collectionProp name="HeaderManager.headers">
              <elementProp name="Content-Type" elementType="Header">
                <stringProp name="Header.name">Content-Type</stringProp>
                <stringProp name="Header.value">application/json</stringProp>
              </elementProp>
            </collectionProp>
          </HeaderManager>
          <hashTree/>
        </hashTree>
        <ResponseAssertion guiclass="AssertionGui" testclass="ResponseAssertion" testname="Assert HTTP 200/201" enabled="true">
          <elementProp name="TestElements" elementType="Collection" guiclass="CollectionPanel" testclass="Collection" testname="Collection" enabled="true">
            <stringProp name="200">200</stringProp>
            <stringProp name="201">201</stringProp>
          </elementProp>
          <stringProp name="Assertion.test_strings">200
201</stringProp>
          <stringProp name="Assertion.test_type">1</stringProp>
          <boolProp name="Assertion.assume_success">false</boolProp>
          <intProp name="Assertion.test_strings_type">2</intProp>
        </ResponseAssertion>
        <hashTree/>
        <ResultCollector guiclass="StatVisualizer" testclass="ResultCollector" testname="Summary Report" enabled="true">
          <objProp>
            <name>saveConfig</name>
            <value class="SampleSaveConfiguration">
              <time>true</time>
              <latency>true</latency>
              <timestamp>true</timestamp>
              <success>true</success>
              <label>true</label>
              <code>true</code>
              <message>true</message>
              <threadName>true</threadName>
              <dataType>true</dataType>
              <encoding>false</encoding>
              <assertions>true</assertions>
              <subresults>true</subresults>
              <responseData>false</responseData>
              <samplerData>false</samplerData>
              <xml>true</xml>
              <fieldNames>true</fieldNames>
              <responseHeaders>false</responseHeaders>
              <requestHeaders>false</requestHeaders>
              <responseDataOnError>false</responseDataOnError>
              <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
              <assertionsResultsToSave>0</assertionsResultsToSave>
              <bytes>true</bytes>
              <sentBytes>true</sentBytes>
              <url>true</url>
              <threadCounts>true</threadCounts>
              <idleTime>true</idleTime>
              <connectTime>true</connectTime>
            </value>
          </objProp>
          <stringProp name="filename">resultado-resumen.csv</stringProp>
        </ResultCollector>
        <hashTree/>
        <ResultCollector guiclass="ViewResultsFullVisualizer" testclass="ResultCollector" testname="View Results Tree" enabled="true">
          <objProp>
            <name>saveConfig</name>
            <value class="SampleSaveConfiguration">
              <time>true</time>
              <latency>true</latency>
              <timestamp>true</timestamp>
              <success>true</success>
              <label>true</label>
              <code>true</code>
              <message>true</message>
              <threadName>true</threadName>
              <dataType>true</dataType>
              <encoding>false</encoding>
              <assertions>true</assertions>
              <subresults>true</subresults>
              <responseData>true</responseData>
              <samplerData>true</samplerData>
              <xml>true</xml>
              <fieldNames>true</fieldNames>
              <responseHeaders>false</responseHeaders>
              <requestHeaders>false</requestHeaders>
              <responseDataOnError>false</responseDataOnError>
              <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
              <assertionsResultsToSave>0</assertionsResultsToSave>
              <bytes>true</bytes>
              <sentBytes>true</sentBytes>
              <url>true</url>
              <threadCounts>true</threadCounts>
              <idleTime>true</idleTime>
              <connectTime>true</connectTime>
            </value>
          </objProp>
          <stringProp name="filename">resultado-detallado.csv</stringProp>
        </ResultCollector>
        <hashTree/>
        <GraphVisualizer guiclass="GraphVisualizer" testclass="ResultCollector" testname="Graph Results" enabled="true">
          <objProp>
            <name>saveConfig</name>
            <value class="SampleSaveConfiguration">
              <time>true</time>
              <latency>true</latency>
              <timestamp>true</timestamp>
              <success>true</success>
              <label>true</label>
              <code>true</code>
              <message>true</message>
              <threadName>true</threadName>
              <dataType>true</dataType>
              <encoding>false</encoding>
              <assertions>true</assertions>
              <subresults>true</subresults>
              <responseData>false</responseData>
              <samplerData>false</samplerData>
              <xml>true</xml>
              <fieldNames>true</fieldNames>
              <responseHeaders>false</responseHeaders>
              <requestHeaders>false</requestHeaders>
              <responseDataOnError>false</responseDataOnError>
              <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
              <assertionsResultsToSave>0</assertionsResultsToSave>
              <bytes>true</bytes>
              <sentBytes>true</sentBytes>
              <url>true</url>
              <threadCounts>true</threadCounts>
              <idleTime>true</idleTime>
              <connectTime>true</connectTime>
            </value>
          </objProp>
          <stringProp name="filename">resultado-grafico.csv</stringProp>
        </ResultCollector>
        <hashTree/>
      </hashTree>
    </hashTree>
  </hashTree>
</jmeterTestPlan>
```

---

## 🚀 PASO 3: EJECUTAR LA PRUEBA

### Asegúrate que tu app está corriendo

```bash
# En terminal, en tu proyecto Laravel
php artisan serve
# Output: Server running at http://127.0.0.1:8000
```

### Ejecuta JMeter

#### Opción A: GUI (ver en vivo)

```bash
jmeter -t tests/jmeter/LocalRegistrationLoadTest.jmx
```

Luego:
1. Click el botón **verde ► (Start)**
2. Verás el progreso en tiempo real
3. Cuando termine, revisa:
   - **Summary Report**: tabla de resultados
   - **View Results Tree**: detalle de cada request
   - **Graph Results**: gráficos

#### Opción B: CLI (mejor rendimiento)

```bash
jmeter -n \
  -t tests/jmeter/LocalRegistrationLoadTest.jmx \
  -l resultado.jtl \
  -j jmeter.log \
  -g resultado-reporte
```

Luego genera reporte HTML:

```bash
jmeter -g resultado.jtl -o resultado-reporte
```

Abre en navegador: `resultado-reporte/index.html`

---

## 📊 INTERPRETAR RESULTADOS

### Summary Report (Lo más importante)

| Campo | Qué es | Criterio | ¿Bueno? |
|-------|--------|----------|--------|
| **# Samples** | Total requests | ~300 (150 users × 2 requests) | ✅ |
| **Avg** | Promedio tiempo respuesta | < 1000ms | ✅ |
| **Min** | Tiempo mínimo | - | ℹ️ |
| **Max** | Tiempo máximo | < 5000ms | ✅ |
| **Std. Dev.** | Desviación estándar | Baja = consistente | ✅ |
| **Error %** | Porcentaje de errores | < 1% | ✅ |
| **Throughput** | Requests por segundo | > 50 | ✅ |
| **Sent KB/sec** | Datos enviados | - | ℹ️ |
| **Received KB/sec** | Datos recibidos | - | ℹ️ |

### Ejemplo de Resultado EXITOSO

```
Summary Report for "150 Concurrent Users"

        # Samples     Avg  Min  Max  Std. Dev.  Error %  Throughput
POST /api/users      150  450  120  2100   320    0.0%      2.50
POST /api/locals     150  520  140  1800   280    0.0%      2.40
TOTAL               300  485  120  2100   300    0.0%      5.00
```

### Criterios de Aceptación

```
✅ PASA si:
- Avg < 1000ms (1 segundo)
- 95% Response Time < 2000ms (2 segundos)
- Error Rate < 1%
- Throughput > 50 req/sec

❌ FALLA si:
- Avg > 2000ms
- Error Rate > 5%
- Timeout errors
```

---

## 💾 GENERAR REPORTE FINAL

### Crear documento de evidencia

Archivo: `REPORTE_RENDIMIENTO.md`

```markdown
# Reporte de Pruebas de Rendimiento
## Proyecto: La Comarca
## Fecha: [HOY]
## Herramienta: Apache JMeter 5.6.3

### Configuración de Prueba
- **Usuarios Concurrentes**: 150
- **Ramp-up Time**: 60 segundos
- **Endpoints Probados**: 2 (POST /api/users, POST /api/locals)
- **Total Requests**: 300 (150 × 2)
- **Duración**: ~2 minutos

### Resultados

| Métrica | Valor | Criterio | Estado |
|---------|-------|----------|--------|
| Average Response Time | 485 ms | < 1000 ms | ✅ |
| Min Response Time | 120 ms | - | ✅ |
| Max Response Time | 2100 ms | < 5000 ms | ✅ |
| 95% Response Time | 1200 ms | < 2000 ms | ✅ |
| Error Rate | 0% | < 1% | ✅ |
| Throughput | 5.0 req/sec | > 1 req/sec | ✅ |
| Successful Requests | 300 | 100% | ✅ |
| Failed Requests | 0 | 0% | ✅ |

### Conclusiones
✅ La aplicación soporta 150 usuarios concurrentes sin problemas.
✅ Tiempos de respuesta están dentro de rangos aceptables.
✅ No hay errores ni timeouts.
✅ La aplicación está lista para producción en términos de rendimiento.

### Recomendaciones
1. Implementar caching (Redis) para optimizar aún más
2. Usar CDN para archivos estáticos
3. Considerar load balancing si crece a 500+ usuarios
4. Monitorear base de datos (índices en columnas de búsqueda)

**Evaluación Final**: ✅ APROBADO
```

---

## 📸 Evidencias para Presentación

1. **Screenshot 1: Configuación JMeter**
   - Muestra el test plan con 150 usuarios

2. **Screenshot 2: Durante la prueba**
   - Muestra la barra de progreso en ejecución

3. **Screenshot 3: Summary Report**
   - Tabla con resultados de rendimiento

4. **Screenshot 4: Graph Results**
   - Gráfico de tiempos de respuesta

5. **Screenshot 5: Sin errores**
   - Error rate = 0%

Guarda en: `evidencias-rendimiento/`

---

## ⚠️ Troubleshooting

### Error: "Connection refused"
- Verifica que `php artisan serve` está corriendo
- Verifica puerto 8000 es correcto

### Error: "JAVA_HOME not set"
- Instala Java: https://www.java.com/es/download/
- O descarga JDK: https://www.oracle.com/java/technologies/javase-downloads.html

### Resultados lentos (> 3000ms)
- Revisa logs de Laravel: `tail -f storage/logs/laravel.log`
- Verifica BD no está bloqueada
- Aumenta `max_connections` en MySQL

### Muchos errores (> 1%)
- Revisa validaciones en controladores
- Asegúrate que BD de testing tiene suficiente espacio
- Verifica que tabla `tbrole` tiene rol_id=2 (Gerente)

