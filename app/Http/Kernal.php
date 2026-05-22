protected $middlewareAliases = [

    'permission' => \App\Http\Middleware\CheckPermission::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,

];