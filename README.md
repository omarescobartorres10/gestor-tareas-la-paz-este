Aquí tienes el README reescrito con un tono natural y personal:

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
  <p><strong>Organizá tareas, charlá con tu equipo y mantené todo el contexto en un solo lugar.</strong></p>
</div>

---

## ¿De qué va esto?

TaskFlow es un gestor de tareas que armamos para que los equipos de la municipalidad de La Paz Este puedan trabajar sin perder el hilo. La mayoría de los tableros kanban te dejan mover tarjetas de una columna a otra y ya está. Acá cada tarea tiene su propio chat, así que las decisiones, las dudas y los acuerdos quedan atados a lo que hay que hacer, no perdidos en un grupo de WhatsApp.

Además, podés mencionar compañeros con @ y ellos reciben una notificación al toque. Todo queda documentado sin esfuerzo extra.

---

## Por qué sirve

- **Contexto unificado:** Cada tarea tiene su conversación. No hay que andar buscando en tres lados distintos para entender por qué se tomó una decisión.
- **Rápido de verdad:** Usamos caché agresiva, consultas optimizadas y Alpine.js para que la interfaz responda sin demoras.
- **Seguro:** Autenticación de Laravel, roles definidos y protección CSRF. Cada quien ve solo lo que le corresponde.
- **Menciones inteligentes:** Escribí @nombre y la persona recibe una notificación. Nada de cadenas de correos interminables.

---

## Lo que podés hacer

- Crear, editar y mover tareas entre columnas
- Asignar responsables y fechas de vencimiento
- Chatear dentro de cada tarea en tiempo real
- Mencionar compañeros con @
- Filtrar y buscar tareas al instante
- Adjuntar archivos directamente a una tarea
- Ver un historial de cambios por tarea

---

## Tecnologías

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Tailwind CSS 4, Alpine.js, Blade
- **Base de datos:** MySQL
- **Tiempo real:** Laravel Echo con Pusher
- **Paquetería:** Vite para assets, Composer para dependencias PHP

---

## Cómo levantarlo en tu máquina

Necesitás tener PHP 8.2 o más, Composer, Node.js con npm y MySQL.

**1. Clonar el repo**

```bash
git clone <url-del-repositorio>
cd gestor-tareas-la-paz-este-main
```

**2. Dependencias de PHP**

```bash
composer install
```

**3. Configurar el entorno**

Copiá el archivo de ejemplo y generá la llave:

```bash
cp .env.example .env
php artisan key:generate
```

Editá el `.env` con los datos de tu base de datos. Si vas a usar Pusher para el tiempo real, configurá también las credenciales ahí.

**4. Base de datos y datos de prueba**

```bash
php artisan migrate --seed
```

Esto crea las tablas y carga algunos usuarios y tareas de ejemplo para que puedas probar.

**5. Dependencias de frontend**

```bash
npm install
npm run build
```

**6. Arrancar**

```bash
php artisan serve
```

Abrí `http://localhost:8000` y listo.

---

## Datos de prueba

Si corriste los seeders, vas a tener un usuario administrador y algunos miembros de equipo cargados. Los detalles exactos están en el archivo `DATOS_DEMO.md` que dejamos en la raíz del proyecto.

---

## La API

TaskFlow expone varios endpoints para integrarse con otros sistemas. Si necesitás conectar algo, revisá `API_DOCUMENTATION.md` donde está todo documentado con ejemplos de requests y responses.

---

## Estructura rápida

- `app/Models/` — Modelos: Tarea, Usuario, Comentario, Proyecto
- `app/Http/Controllers/` — Lógica de negocio y endpoints
- `app/Events/` — Eventos para notificaciones en tiempo real
- `routes/web.php` — Rutas de la interfaz web
- `routes/api.php` — Endpoints de la API
- `resources/views/` — Vistas en Blade con componentes de Alpine.js
- `database/migrations/` — Esquema de la base de datos

---

*Hecho para que los equipos de La Paz Este se enfoquen en hacer, no en coordinarse.*
