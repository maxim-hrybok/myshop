<?php
session_start();

// Generate CSRF token for the session (Validation moved to CsrfMiddleware)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/vendor/autoload.php';

// 1. Build Container & Config
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/dependencies.php');
$container = $containerBuilder->build();

$config = $container->get(\App\Config\ConfigService::class);

// 2. Global Security Headers
header_remove('X-Powered-By');
header('X-Frame-Options: DENY'); 
header('X-Content-Type-Options: nosniff'); 

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']), 
    'httponly' => true,                   
    'samesite' => 'Strict'                
]);

// 3. Error Reporting
if ($config->get('app.env') === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 4. Setup Smarty Globals
$smarty = $container->get(\Smarty\Smarty::class);
$smarty->assign('session', $_SESSION);
$smarty->assign('csrf_token', $_SESSION['csrf_token']);
$smarty->assign('recaptcha_site_key', $config->get('recaptcha.site_key'));

$cartItemCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItemCount = array_sum($_SESSION['cart']);
}
$smarty->assign('cartItemCount', $cartItemCount);

// === 5. LOAD ROUTES ===
// Include the closure from web.php and pass it to the dispatcher
$routeDefinitions = require __DIR__ . '/routes/web.php';
$dispatcher = FastRoute\simpleDispatcher($routeDefinitions);

// === 6. DISPATCH THE REQUEST ===
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header("HTTP/1.0 404 Not Found");
        $smarty->assign('pageTitle', 'Not Found');
        $smarty->display('errors/404.tpl');
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header("HTTP/1.0 405 Method Not Allowed");
        echo '405 - Method Not Allowed';
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1]; 
        $vars = $routeInfo[2];

        $controllerClass = $handler[0];
        $method = $handler[1];
        $routeMiddlewares = $handler[2] ?? [];

        // 7. EXECUTE MIDDLEWARE PIPELINE
        // Global middlewares run first on every single request
        $globalMiddlewares = [
            \App\Middleware\CsrfMiddleware::class
        ];

        $allMiddlewares = array_merge($globalMiddlewares, $routeMiddlewares);

        foreach ($allMiddlewares as $mwClass) {
            $middleware = $container->get($mwClass);
            $middleware->handle($uri, $httpMethod);
        }

        // 8. EXECUTE CONTROLLER
        $controller = $container->get($controllerClass);
        $controller->$method($vars);
        break;
}