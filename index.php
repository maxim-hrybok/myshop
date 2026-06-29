<?php
session_start();

// 1. Generate a cryptographically secure CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. Global CSRF Protection Middleware
// Intercept ALL POST requests and verify the token BEFORE routing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    // hash_equals prevents timing attacks during string comparison
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        die("403 Forbidden: Invalid or missing CSRF Token. Please refresh the page and try again.");
    }
}

require_once __DIR__ . '/vendor/autoload.php';

// 1. Load Environment Variables First
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// 2. Build Container FIRST, so we can access the ConfigService safely
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/dependencies.php');
$container = $containerBuilder->build();

// 3. Add Strict Security Headers (Fixes: "Missing Anti-clickjacking" & "X-Content-Type-Options Missing") + Fetch ConfigService
$config = $container->get(\App\Config\ConfigService::class);


header_remove('X-Powered-By');
header('X-Frame-Options: DENY'); // Prevents your site from being loaded in an iframe
header('X-Content-Type-Options: nosniff'); // Prevents browsers from guessing file types

// Secure Session Cookies (Fixes: "Cookie No HttpOnly Flag" & "Cookie without SameSite Attribute")
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']), // Only sends cookies over HTTPS (if enabled)
    'httponly' => true,                   // Blocks JavaScript from stealing the session (XSS protection)
    'samesite' => 'Strict'                // Blocks cookies from being sent cross-site (CSRF protection)
]);

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

$cartItemCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItemCount = array_sum($_SESSION['cart']);
}
$smarty->assign('cartItemCount', $cartItemCount);//#########################################################################saved part 
// Pass public reCAPTCHA key to frontend
$smarty->assign('recaptcha_site_key', $config->get('recaptcha.site_key'));

// 2. === ROUTE DEFINITIONS ===
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Public Products
    $r->addRoute('GET', '/', ['App\Controllers\ProductController', 'showAll']);
    $r->addRoute('GET', '/products', ['App\Controllers\ProductController', 'showAll']);
    $r->addRoute('GET', '/product/{id:\d+}', ['App\Controllers\ProductController', 'show']);
    $r->addRoute('POST', '/product/{id:\d+}/comment', ['App\Controllers\ProductController', 'addComment']);
    $r->addRoute('GET', '/about', ['App\Controllers\PageController', 'about']);

    // Auth
    $r->addRoute('GET', '/login',['App\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/login',['App\Controllers\AuthController', 'handleLogin']);
    $r->addRoute('GET', '/register',['App\Controllers\AuthController', 'showRegisterForm']);
    $r->addRoute('POST', '/register',['App\Controllers\AuthController', 'handleRegister']);
    $r->addRoute('GET', '/logout',['App\Controllers\AuthController', 'logout']);

    // Admin Products
    $r->addRoute('GET', '/admin',['App\Controllers\AdminController', 'dashboard']);
    $r->addRoute('GET', '/admin/create', ['App\Controllers\AdminController', 'create']);
    $r->addRoute('POST', '/admin/store', ['App\Controllers\AdminController', 'store']);
    $r->addRoute('GET', '/admin/edit/{id:\d+}', ['App\Controllers\AdminController', 'edit']);
    $r->addRoute('POST', '/admin/update/{id:\d+}',['App\Controllers\AdminController', 'update']);
    $r->addRoute('POST', '/admin/delete/{id:\d+}',['App\Controllers\AdminController', 'delete']);

    // Admin Categories
    $r->addRoute('GET', '/admin/categories', ['App\Controllers\CategoryController', 'index']);
    $r->addRoute('GET', '/admin/categories/create', ['App\Controllers\CategoryController', 'create']);
    $r->addRoute('POST', '/admin/categories/store',['App\Controllers\CategoryController', 'store']);
    $r->addRoute('GET', '/admin/categories/edit/{id:\d+}',['App\Controllers\CategoryController', 'edit']);
    $r->addRoute('POST', '/admin/categories/update/{id:\d+}', ['App\Controllers\CategoryController', 'update']);
    $r->addRoute('POST', '/admin/categories/delete/{id:\d+}',['App\Controllers\CategoryController', 'delete']);
    
    // Admin Comments
    $r->addRoute('GET', '/admin/comments', ['App\Controllers\AdminController', 'showComments']);
    $r->addRoute('POST', '/admin/comments/approve/{id:\d+}', ['App\Controllers\AdminController', 'approveComment']);
    $r->addRoute('POST', '/admin/comments/delete/{id:\d+}',['App\Controllers\AdminController', 'deleteComment']);

    // Cart & Orders
    $r->addRoute('GET', '/cart',['App\Controllers\CartController', 'view']);
    $r->addRoute('POST', '/cart/add/{id:\d+}', ['App\Controllers\CartController', 'add']);
    $r->addRoute('GET', '/cart/add/{id:\d+}', ['App\Controllers\CartController', 'add']); 
    $r->addRoute('GET', '/cart/remove/{id:\d+}',['App\Controllers\CartController', 'remove']);
    $r->addRoute('POST', '/checkout',['App\Controllers\CartController', 'checkout']);
    $r->addRoute('GET', '/orders',['App\Controllers\OrderController', 'index']);

   // Admin Order Routes
    $r->addRoute('GET', '/admin/orders',['App\Controllers\AdminController', 'showOrders']);
    $r->addRoute('GET', '/admin/orders/edit/{id:\d+}', ['App\Controllers\AdminController', 'editOrder']);
    $r->addRoute('POST', '/admin/orders/update/{id:\d+}', ['App\Controllers\AdminController', 'updateOrderStatus']);
    $r->addRoute('POST', '/admin/orders/delete/{id:\d+}',['App\Controllers\AdminController', 'deleteOrder']);
});

// === DISPATCH THE REQUEST ===
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// === HANDLE THE ROUTE ===
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

        // $controller = null;

        // if ($controllerClass === 'App\Controllers\CartController') {
        //     $prodModel = new \App\Models\ProductModel($pdo);
        //     $ordModel = new \App\Models\OrderModel($pdo);
        //     $cartService = new \App\Services\CartService($prodModel, $ordModel, $pdo);
        //     $controller = new \App\Controllers\CartController($cartService, $smarty);

        // } elseif ($controllerClass === 'App\Controllers\ProductController') {
        //     $prodModel = new \App\Models\ProductModel($pdo);
        //     $catModel = new \App\Models\CategoryModel($pdo);
        //     $controller = new \App\Controllers\ProductController($prodModel, $catModel, $smarty);

        // } elseif ($controllerClass === 'App\Controllers\AuthController') {
        //     $controller = new \App\Controllers\AuthController($pdo, $smarty);

        // } elseif ($controllerClass === 'App\Controllers\AdminController') {
        //      $controller = new \App\Controllers\AdminController($pdo, $smarty);

        // } elseif ($controllerClass === 'App\Controllers\CategoryController') {
        //      $controller = new \App\Controllers\CategoryController($pdo, $smarty);
             
        // } elseif ($controllerClass === 'App\Controllers\OrderController') {
        //      $controller = new \App\Controllers\OrderController($pdo, $smarty);
        // }

        // if ($controller) {
        //     $controller->$method($vars);
        // } else {
        //     echo "Error: Controller not configured in index.php";
        // }
        // break;

        // --- PHP-DI AUTOWIRING  ---
        // PHP-DI reads the constructor of $controllerClass, sees it needs PDO and Smarty (or a Service),
        // and automatically builds the entire dependency tree. No manual instantiation required
        $controller = $container->get($controllerClass);
        $controller->$method($vars);
        break;

}
?>

<?php //require_once __DIR__ . '/../components/footer.php'; ?>
