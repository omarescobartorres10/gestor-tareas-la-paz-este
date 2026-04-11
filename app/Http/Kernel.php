// En la sección 'routeMiddleware', agregar:
protected $routeMiddleware = [
    // ... existing middleware ...
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
