# TaskFlow API Documentation

## Visión General

TaskFlow proporciona endpoints REST para gestión de tareas, comentarios y administración de usuarios.

**Base URL**: `http://localhost:8000`  
**Autenticación**: Laravel Session (Cookie-based)  
**CSRF**: Requerido en todos los endpoints POST/PATCH/DELETE

---

## Autenticación

### Login
```http
POST /login
Content-Type: application/x-www-form-urlencoded

email=user@example.com&password=password123
```

**Response**: Redirect to `/tasks`

---

## Tasks (Tareas)

### Listar Tareas
```http
GET /tasks?view={view}&status={status}&priority={priority}&search={search}
```

**Query Parameters**:
- `view`: `my_tasks` | `my_tracking` | `all` (default: `my_tasks`)
- `status`: `Pendiente` | `En progreso` | `Completada`
- `priority`: `Baja` | `Media` | `Alta`
- `search`: Búsqueda por título o descripción

**Response**: HTML (Blade view)

**Rate Limit**: None

---

### Ver Tarea
```http
GET /tasks/{id}
```

**Authorization**: 
- Creator
- Assignee
- Admin

**Response**: HTML (Blade view) con detalles de tarea y comentarios paginados

---

### Crear Tarea
```http
POST /tasks
Content-Type: application/x-www-form-urlencoded
X-CSRF-TOKEN: {token}

title=Nueva+Tarea&description=Descripción&assignee_id=2&priority=Alta&start_date=2025-12-20&due_date=2025-12-27
```

**Request Body**:
```json
{
  "title": "string (required, max:255)",
  "description": "string (optional)",
  "assignee_id": "integer (required, must be active user)",
  "priority": "enum (required): Baja|Media|Alta",
  "start_date": "date (required, format: Y-m-d)",
  "due_date": "date (required, >= start_date)"
}
```

**Rate Limit**: 20 requests/minute

**Response**: Redirect to `/tasks` with success message

**Validations**:
- `assignee_id` must be an active user
- `due_date` must be >= `start_date`

---

### Actualizar Tarea
```http
PATCH /tasks/{id}
Content-Type: application/x-www-form-urlencoded
X-CSRF-TOKEN: {token}

status=En+progreso
```

**Request Body** (all fields optional):
```json
{
  "title": "string (max:255)",
  "description": "string",
  "assignee_id": "integer (must be active user)",
  "priority": "enum: Baja|Media|Alta",
  "start_date": "date",
  "due_date": "date (>= start_date)",
  "status": "enum: Pendiente|En progreso|Completada"
}
```

**Authorization**:
- Creator
- Assignee
- Admin

**Rate Limit**: 30 requests/minute

**Response**: 
- AJAX: `{"success": "Tarea actualizada", "task": {...}}`
- Form: Redirect back with success message

---

### Archivar Tarea
```http
PATCH /tasks/{id}/archive
X-CSRF-TOKEN: {token}
```

**Authorization**:
- Creator
- Admin

**Response**: Redirect to `/tasks`

---

### Desarchivar Tarea
```http
PATCH /tasks/{id}/unarchive
X-CSRF-TOKEN: {token}
```

**Authorization**:
- Creator
- Assignee
- Admin

**Response**: Redirect to `/tasks`

---

## Comments (Comentarios)

### Crear Comentario
```http
POST /tasks/{taskId}/comments
Content-Type: application/x-www-form-urlencoded
X-CSRF-TOKEN: {token}

content=Este+es+un+comentario+con+@usuario+mencionado
```

**Request Body**:
```json
{
  "content": "string (required, max:5000)"
}
```

**Rate Limit**: 10 requests/minute

**Menciones**:
- Usar `@nombre` o `@email` para mencionar usuarios
- Usuarios mencionados tendrán acceso al comentario

**Response**: Redirect back with success message

---

### Eliminar Comentario
```http
DELETE /comments/{id}
X-CSRF-TOKEN: {token}
```

**Authorization**:
- Comment author
- Admin

**Response**: Redirect back with success message

---

## Admin (Administración)

### Dashboard
```http
GET /admin
```

**Authorization**: Admin only

**Response**: HTML con estadísticas:
- Total usuarios activos
- Total tareas
- Tasa de finalización
- Estadísticas por usuario

**Performance**: Cached for 5 minutes

---

### Listar Usuarios
```http
GET /admin/users
```

**Authorization**: Admin only

**Response**: HTML con lista de todos los usuarios (activos e inactivos)

---

### Crear Usuario
```http
POST /admin/users
Content-Type: application/x-www-form-urlencoded
X-CSRF-TOKEN: {token}

name=Nuevo+Usuario&email=nuevo@example.com&password=password123&password_confirmation=password123&role=usuario&department=IT&position=Developer
```

**Request Body**:
```json
{
  "name": "string (required, max:255)",
  "email": "string (required, email, unique)",
  "password": "string (required, min:8, confirmed)",
  "password_confirmation": "string (required)",
  "role": "enum (required): admin|usuario",
  "department": "string (optional)",
  "position": "string (optional)"
}
```

**Authorization**: Admin only

**Response**: Redirect to `/admin/users`

---

### Actualizar Usuario
```http
PATCH /admin/users/{id}
X-CSRF-TOKEN: {token}

name=Nombre+Actualizado&email=user@example.com&role=usuario&is_active=0
```

**Request Body**:
```json
{
  "name": "string (required, max:255)",
  "email": "string (required, email, unique except self)",
  "role": "enum (optional): admin|usuario",
  "department": "string (optional)",
  "position": "string (optional)",
  "is_active": "boolean (optional)"
}
```

**Authorization**: Admin only

**Restrictions**:
- Admin cannot deactivate themselves

**Response**: Redirect back with success message

---

## Rate Limiting

| Endpoint | Limit | Window |
|----------|-------|--------|
| `POST /tasks` | 20 | 1 minute |
| `PATCH /tasks/{id}` | 30 | 1 minute |
| `POST /tasks/{id}/comments` | 10 | 1 minute |

**Response on Limit Exceeded**:
```http
HTTP/1.1 429 Too Many Requests
```

---

## Error Responses

### Validation Error
```http
HTTP/1.1 302 Found
Location: {previous_url}

Session includes 'errors' with validation messages
```

### Authorization Error
```http
HTTP/1.1 403 Forbidden
```

### Not Found
```http
HTTP/1.1 404 Not Found
```

---

## AJAX Endpoints

### Update Task Status (AJAX)
```http
PATCH /tasks/{id}
X-CSRF-TOKEN: {token}
X-Requested-With: XMLHttpRequest
Accept: application/json

status=Completada
```

**Response**:
```json
{
  "success": "Tarea actualizada",
  "task": {
    "id": 1,
    "title": "...",
    "status": "Completada",
    ...
  }
}
```

---

## Best Practices

### CSRF Protection
Siempre incluir token CSRF en requests POST/PATCH/DELETE:

```javascript
fetch('/tasks/1', {
  method: 'PATCH',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Content-Type': 'application/x-www-form-urlencoded',
  },
  body: 'status=Completada'
})
```

### Paginación
Los comentarios están paginados (10 por página). URLs incluyen `?page=N` automáticamente.

### Búsqueda
La búsqueda usa debounce de 500ms en el frontend para evitar sobrecarga.

---

## Códigos de Estado

| Código | Significado |
|--------|-------------|
| 200 | OK - Petición exitosa |
| 201 | Created - Recurso creado |
| 302 | Found - Redirect (común en Laravel) |
| 403 | Forbidden - Sin permisos |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Validación fallida |
| 429 | Too Many Requests - Rate limit excedido |
| 500 | Internal Server Error - Error del servidor |

---

## Notas de Seguridad

1. **Autenticación requerida**: Todos los endpoints requieren sesión activa
2. **CSRF Protection**: Obligatorio en endpoints mutadores
3. **Rate Limiting**: Protege contra abuso
4. **Input Validation**: Todos los inputs son validados
5. **XSS Protection**: Contenido escapado en vistas
6. **Authorization**: Policies verifican permisos

---

**Versión**: 2.5  
**Última actualización**: 19 Diciembre 2025
