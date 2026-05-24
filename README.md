Aquí tienes una versión profesional y en primera persona, manteniendo un tono serio sin voseo:

---

<div align="center">
  <h1>📋 TaskFlow</h1>
  <h3>Gestor de Tareas Colaborativo</h3>
  <p>
    <img src="https://img.shields.io/badge/Laravel-12-red?style=flat-square&logo=laravel" alt="Laravel 12">
    <img src="https://img.shields.io/badge/Tailwind_CSS-4-38bdf8?style=flat-square&logo=tailwindcss" alt="Tailwind CSS">
    <img src="https://img.shields.io/badge/Alpine.js-8bc0d0?style=flat-square&logo=alpine.js" alt="Alpine.js">
    <img src="https://img.shields.io/badge/PHP-8.2+-777bb3?style=flat-square&logo=php" alt="PHP 8.2+">
  </p>
  <p><strong>Gestión de tareas con chat integrado y menciones en tiempo real para equipos municipales.</strong></p>
</div>

---

## Acerca del proyecto

TaskFlow es un sistema de gestión de tareas que desarrollé para los equipos internos de la municipalidad de La Paz Este. A diferencia de un tablero kanban convencional, cada tarea incorpora un chat en tiempo real donde los miembros del equipo pueden discutir detalles, resolver dudas y tomar decisiones sin que la información quede dispersa en canales externos.

El sistema permite mencionar a otros usuarios con el formato @nombre, lo que dispara una notificación inmediata. De esta forma, el contexto de cada tarea se mantiene centralizado y documentado de manera natural.

---

## Funcionalidades principales

- Tablero kanban con columnas personalizables
- Creación, edición y eliminación de tareas
- Asignación de responsables y fechas de vencimiento
- Chat en tiempo real dentro de cada tarea
- Menciones con @usuario y notificaciones instantáneas
- Filtros y búsqueda de tareas
- Adjuntar archivos a tareas
- Historial de cambios por tarea
- Roles de usuario con permisos diferenciados

---

## Tecnologías utilizadas

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Tailwind CSS 4, Alpine.js, Blade
- **Base de datos:** MySQL
- **Tiempo real:** Laravel Echo con Pusher
- **Herramientas:** Vite, Composer

---

## Instalación y configuración local

### Requisitos previos

- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL

### Pasos

**1. Clonar el repositorio**

```bash
git clone <url-del-repositorio>
cd gestor-tareas-la-paz-este-main
```

**2. Instalar dependencias de PHP**

```bash
composer install
```

**3. Configurar el entorno**

```bash
cp .env.example .env
php artisan key:generate
```

Editar el archivo `.env` con las credenciales de la base de datos. Para la funcionalidad de tiempo real es necesario configurar las credenciales de Pusher en este mismo archivo.

**4. Migraciones y datos de prueba**

```bash
php artisan migrate --seed
```

Este comando genera la estructura de la base de datos y carga datos de prueba.

**5. Dependencias de frontend**

```bash
npm install
npm run build
```

**6. Iniciar el servidor**

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

---

## Datos de prueba

Los seeders generan un usuario administrador y varios miembros de equipo con tareas de ejemplo. Las credenciales y el detalle de los datos precargados se encuentran en el archivo `DATOS_DEMO.md`.

---

## API

El sistema expone endpoints para integración con otros sistemas. La documentación completa, con ejemplos de peticiones y respuestas, está disponible en `API_DOCUMENTATION.md`.

---

## Estructura del proyecto

- `app/Models/` — Modelos: Tarea, Usuario, Comentario, Proyecto
- `app/Http/Controllers/` — Lógica de negocio y controladores
- `app/Events/` — Eventos del sistema de notificaciones en tiempo real
- `routes/web.php` — Rutas de la interfaz web
- `routes/api.php` — Endpoints de la API
- `resources/views/` — Vistas Blade con componentes de Alpine.js
- `database/migrations/` — Esquema de la base de datos

---

*Desarrollado para optimizar la coordinación de los equipos internos de La Paz Este.*
