# 📊 Llenado de Datos de Demostración

Este documento explica cómo llenar el sistema con datos de prueba para demostración.

## 🚀 Método Rápido (Recomendado)

Usa el comando artisan personalizado:

```bash
php artisan demo:fill
```

Este comando:
- ✅ Crea usuarios de ejemplo (si no existen)
- ✅ Crea 24 tareas realistas de diferentes departamentos
- ✅ Agrega comentarios a las tareas
- ✅ Genera datos para todos los reportes

### Reiniciar Base de Datos

Si quieres empezar desde cero (⚠️ **elimina todos los datos**):

```bash
php artisan demo:fill --fresh
```

---

## 📝 Método Manual

### 1. Crear Usuarios (si no existen)

```bash
php artisan db:seed --class=UserSeeder
```

### 2. Crear Tareas

```bash
php artisan db:seed --class=TaskSeeder
```

---

## 📊 Datos Generados

### Tareas (24 total)

| Estado | Cantidad |
|--------|----------|
| Completadas | 5 |
| En Progreso | 8 |
| Pendientes | 11 |

| Prioridad | Cantidad |
|-----------|----------|
| Alta | 11 |
| Media | 8 |
| Baja | 5 |

### Departamentos Incluidos

- 🏢 Administración
- 💻 Desarrollo/TI
- 👥 Recursos Humanos
- 🚰 Servicios Públicos
- 🏗️ Obras Públicas
- 📞 Atención Ciudadana

### Características de las Tareas

- ✅ Tareas completadas (para métricas de productividad)
- ⏰ Tareas vencidas (para análisis de tiempos)
- 📅 Tareas próximas a vencer
- 💬 Comentarios en las tareas
- 📊 Distribución realista de prioridades

---

## 🔐 Credenciales de Acceso

Las credenciales se generan al ejecutar el seeder. Corré el siguiente comando y seguilás las instrucciones en pantalla:

```bash
php artisan db:seed --class=CreateTestUsers
```

---

## 📈 Verificar Reportes

Después de llenar los datos, visita:

```
http://localhost/sistema-de-gestion-de-tareas/public/admin/reports
```

Deberías ver:
- ✅ Estadísticas generales con números reales
- ✅ Análisis de tiempos (tareas vencidas, tiempo promedio, etc.)
- ✅ Gráficos de tendencias con datos
- ✅ Productividad por usuario
- ✅ Rendimiento por departamento
- ✅ Exportación PDF/CSV funcional

---

## 🧹 Limpiar Datos

Para eliminar solo las tareas (mantener usuarios):

```bash
php artisan db:seed --class=TaskSeeder
```

Para reiniciar completamente:

```bash
php artisan migrate:fresh
php artisan demo:fill
```

---

## 💡 Notas

- Los datos son **realistas** basados en tareas municipales típicas
- Las fechas están distribuidas en los últimos 30 días
- Algunos usuarios tienen más tareas que otros (distribución natural)
- Las tareas incluyen diferentes estados para análisis completo
- Los comentarios se generan automáticamente en ~60% de las tareas

---

## 🎯 Casos de Uso

### Para Demostración
```bash
php artisan demo:fill --fresh
```

### Para Desarrollo/Testing
```bash
php artisan demo:fill
```

### Para Producción
⚠️ **NO ejecutar estos comandos en producción**. Son solo para desarrollo y demostración.
