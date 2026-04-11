# 🔥 Pruebas de Estrés - TaskFlow

Este directorio contiene herramientas y configuraciones para realizar pruebas de estrés en la aplicación TaskFlow.

## 📋 Tabla de Contenidos

- [Requisitos](#requisitos)
- [Tipos de Pruebas](#tipos-de-pruebas)
- [Uso Rápido](#uso-rápido)
- [Comandos Artisan](#comandos-artisan)
- [Artillery (HTTP Load Testing)](#artillery-http-load-testing)
- [Interpretación de Resultados](#interpretación-de-resultados)
- [Umbrales de Rendimiento](#umbrales-de-rendimiento)

## 📦 Requisitos

### Para pruebas Artisan (Laravel)
- PHP 8.1+
- Laravel framework
- Base de datos MySQL configurada

### Para pruebas Artillery
- Node.js 16+
- npm o yarn
- Artillery CLI (`npm install -g artillery`)

## 🧪 Tipos de Pruebas

### 1. **Database Operations** (`database`)
Prueba operaciones CRUD en la base de datos:
- Creación masiva de tareas
- Lecturas con eager loading
- Actualizaciones de estado
- Medición de tiempos de respuesta

### 2. **Cache Operations** (`cache`)
Evalúa el rendimiento del sistema de caché:
- Escrituras y lecturas de caché
- Tasa de aciertos (hit rate)
- Patrón `remember` de Laravel

### 3. **Search Operations** (`search`)
Prueba las funciones de búsqueda:
- Búsqueda full-text
- Fallback a LIKE
- Rendimiento con diferentes términos

### 4. **Concurrent Writes** (`concurrent_writes`)
Simula escrituras concurrentes:
- Creación simultánea de comentarios
- Actualizaciones de estado concurrentes
- Detección de deadlocks
- Manejo de race conditions

### 5. **Memory Usage** (`memory`)
Monitorea el uso de memoria:
- Carga de datos con relaciones
- Procesamiento de colecciones
- Detección de memory leaks
- Estadísticas de memoria pico

## 🚀 Uso Rápido

### Ejecutar todas las pruebas
```powershell
php artisan test:stress
```

### Ejecutar con limpieza automática
```powershell
php artisan test:stress --cleanup
```

### Usar el script interactivo
```powershell
.\tests\stress\run-stress-tests.ps1 -TestType menu
```

## ⚙️ Comandos Artisan

### Sintaxis completa
```powershell
php artisan test:stress [opciones]
```

### Opciones disponibles

| Opción | Descripción | Default |
|--------|-------------|---------|
| `--concurrent=N` | Número de operaciones concurrentes | 10 |
| `--iterations=N` | Total de iteraciones | 100 |
| `--type=TYPE` | Tipo de prueba (database, cache, search, concurrent_writes, memory, all) | all |
| `--cleanup` | Limpiar datos de prueba al finalizar | false |

### Ejemplos

```powershell
# Prueba ligera de base de datos
php artisan test:stress --type=database --iterations=50

# Prueba intensiva de caché
php artisan test:stress --type=cache --iterations=500

# Prueba de concurrencia con 20 operaciones simultáneas
php artisan test:stress --type=concurrent_writes --concurrent=20 --iterations=200

# Todas las pruebas con limpieza
php artisan test:stress --type=all --iterations=100 --cleanup
```

## 🎯 Artillery (HTTP Load Testing)

Artillery simula múltiples usuarios accediendo a la aplicación vía HTTP.

### Instalación
```powershell
npm install -g artillery
```

### Ejecutar con el script
```powershell
.\tests\stress\run-stress-tests.ps1 -UseArtillery -BaseUrl "http://localhost"
```

### Ejecutar directamente
```powershell
artillery run tests/stress/artillery-config.yml
```

### Generar reporte HTML
```powershell
artillery run tests/stress/artillery-config.yml --output report.json
artillery report report.json --output stress-report.html
```

### Escenarios incluidos

1. **Guest - Visit Login Page** (20%)
   - Simula visitantes no autenticados

2. **Auth User - Browse Tasks** (30%)
   - Login y navegación de tareas
   - Aplicación de filtros

3. **Search Tasks** (15%)
   - Búsqueda con diferentes términos

4. **Create Task** (10%)
   - Creación de nuevas tareas

5. **Poll Notifications** (15%)
   - Consulta periódica de notificaciones

6. **Admin Dashboard** (10%)
   - Acceso al panel de administración

### Fases de carga

| Fase | Duración | Usuarios/seg | Descripción |
|------|----------|--------------|-------------|
| Warm up | 30s | 5 | Calentamiento |
| Ramp up | 60s | 10→50 | Incremento gradual |
| Sustained | 120s | 50 | Carga sostenida |
| Spike | 30s | 100 | Pico de carga |

## 📊 Interpretación de Resultados

### Métricas clave

#### Database
- **avg_write_time**: Tiempo promedio de escritura (< 50ms ideal)
- **avg_read_time**: Tiempo promedio de lectura (< 20ms ideal)
- **avg_update_time**: Tiempo promedio de actualización (< 30ms ideal)

#### Cache
- **cache_hit_rate**: Porcentaje de aciertos (> 95% ideal)
- **avg_read_time**: Tiempo de lectura de caché (< 5ms ideal)

#### Search
- **avg_search_time**: Tiempo promedio de búsqueda (< 100ms ideal)
- **max_search_time**: Peor caso (< 500ms aceptable)

#### Concurrent Writes
- **success_rate**: Porcentaje de éxito (> 95% ideal)
- **deadlock_count**: Número de deadlocks (0 ideal)

#### Memory
- **peak_memory_mb**: Uso máximo (< 256MB ideal)
- **memory_growth_mb**: Crecimiento durante prueba (< 10MB ideal)

## ⚠️ Umbrales de Rendimiento

El sistema evalúa automáticamente los resultados:

| Métrica | ✅ Bueno | ⚠️ Advertencia | ❌ Problema |
|---------|----------|----------------|-------------|
| DB Write Time | < 50ms | 50-100ms | > 100ms |
| DB Read Time | < 20ms | 20-50ms | > 50ms |
| Cache Hit Rate | > 95% | 90-95% | < 90% |
| Search Time | < 100ms | 100-300ms | > 300ms |
| Write Success Rate | > 95% | 90-95% | < 90% |
| Peak Memory | < 256MB | 256-512MB | > 512MB |

## 🔧 Solución de Problemas

### Rendimiento de base de datos bajo
1. Verificar índices en tablas `tasks`, `users`, `comments`
2. Revisar consultas con `EXPLAIN`
3. Optimizar eager loading

### Mucha memoria usada
1. Implementar `chunk()` para procesar grandes colecciones
2. Usar `lazy()` en lugar de `get()` para colecciones grandes
3. Limpiar variables no usadas con `unset()`

### Deadlocks frecuentes
1. Ordenar operaciones de forma consistente
2. Reducir tiempo dentro de transacciones
3. Considerar uso de locks optimistas

### Búsquedas lentas
1. Crear índice FULLTEXT en MySQL:
   ```sql
   ALTER TABLE tasks ADD FULLTEXT INDEX ft_search (title, description);
   ```
2. Considerar Elasticsearch para búsquedas complejas

## 📝 Notas Importantes

- Ejecutar pruebas en ambiente de desarrollo/staging, nunca en producción
- Las pruebas con `--cleanup` eliminarán datos de prueba pero pueden afectar caché
- Artillery requiere que el servidor Laravel esté ejecutándose
- Monitorear logs de Laravel durante las pruebas para detectar errores

## 🤝 Contribución

Para agregar nuevos tipos de pruebas:
1. Editar `app/Console/Commands/StressTest.php`
2. Agregar método `private function testNewFeature()`
3. Incluir en el switch de `runTestType()`
4. Agregar umbrales en `assessPerformance()`
