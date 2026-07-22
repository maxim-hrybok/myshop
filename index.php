<?php

// Secure Session Cookies (Fixes: "Cookie No HttpOnly Flag" & "Cookie without SameSite Attribute")
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']), // Only sends cookies over HTTPS (if enabled)
    'httponly' => true,                   // Blocks JavaScript from stealing the session (XSS protection)
    'samesite' => 'Strict'                // Blocks cookies from being sent cross-site (CSRF protection)
]);

session_start();

// 1. Generate a cryptographically secure CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/vendor/autoload.php';

// // 2. Global CSRF Protection Middleware
// // Intercept ALL POST requests and verify the token BEFORE routing
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $submittedToken = $_POST['csrf_token'] ?? '';
//     // hash_equals prevents timing attacks during string comparison
//     if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
//         http_response_code(403);
//         die("403 Forbidden: Invalid or missing CSRF Token. Please refresh the page and try again.");
//     }
// } mooved to CsrfMiddleware.php


// 1. Load Environment Variables First
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad(); // for missing .env file, use safeLoad() instead of load()


// 2. Build Container FIRST, so we can access the ConfigService safely
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/dependencies.php');
$container = $containerBuilder->build();

// 3. Add Strict Security Headers (Fixes: "Missing Anti-clickjacking" & "X-Content-Type-Options Missing") + Fetch ConfigService
$config = $container->get(\App\Config\ConfigService::class);


header_remove('X-Powered-By');
header('X-Frame-Options: DENY'); // Prevents your site from being loaded in an iframe
header('X-Content-Type-Options: nosniff'); // Prevents browsers from guessing file types



// 4. Configure Error Reporting securely based on environment
if ($config->get('app.env') === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 5. Get Smarty from the container
$smarty = $container->get(\Smarty\Smarty::class);

// Make session data available to ALL Smarty templates
$smarty->assign('session', $_SESSION);

// Make the CSRF token easily accessible in templates
$smarty->assign('csrf_token', $_SESSION['csrf_token']);

// Pass public reCAPTCHA key to frontend
$smarty->assign('recaptcha_site_key', $config->get('recaptcha.site_key'));

$cartItemCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItemCount = array_sum($_SESSION['cart']);
}

$smarty->assign('cartItemCount', $cartItemCount);//#########################################################################saved part 

// === 6. ROUTE DEFINITIONS WITH MIDDLEWARE ===
// Format: [ControllerClass, MethodName, [ArrayOfMiddlewareClasses]]
// === LOAD ROUTES ===
// Include the closure from web.php and pass it to the dispatcher
$routeDefinitions = require __DIR__ . '/routes/web.php';
$dispatcher = FastRoute\simpleDispatcher($routeDefinitions);


// === 7. DISPATCH THE REQUEST ===
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// === 8. HANDLE THE ROUTE ===
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

        $globalMiddlewares = [
            \App\Middleware\CsrfMiddleware::class
        ];

        $allMiddlewares = array_merge($globalMiddlewares, $routeMiddlewares);

        foreach ($allMiddlewares as $mwClass) {
            // Instantiate middleware via DI Container and run it
            $middleware = $container->get($mwClass);
            $middleware->handle($uri, $httpMethod);
        }
        // --- PHP-DI AUTOWIRING  ---
        // PHP-DI reads the constructor of $controllerClass, sees it needs PDO and Smarty (or a Service),
        // and automatically builds the entire dependency tree. No manual instantiation required
        $controller = $container->get($controllerClass);
        $controller->$method($vars);
        break;

}
?>

<?php //require_once __DIR__ . '/../components/footer.php'; ?>
