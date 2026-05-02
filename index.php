<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

// 1. Load Environment Variables First
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 2. Configure Error Reporting securely based on environment
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 3. Connect to the database and smarty (config)
require_once __DIR__ . '/config/database.php';
$smarty = require_once __DIR__ . '/config/smarty.php';


$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/dependencies.php');
$container = $containerBuilder->build();

$smarty = $container->get(\Smarty\Smarty::class);// ------------------------------------------------------------------------


// Make session data available to ALL Smarty templates
$smarty->assign('session', $_SESSION);

$cartItemCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItemCount = array_sum($_SESSION['cart']);
}
$smarty->assign('cartItemCount', $cartItemCount);//#########################################################################saved part 
// Pass public reCAPTCHA key to frontend
$smarty->assign('recaptcha_site_key', $_ENV['RECAPTCHA_SITE_KEY'] ?? '');
// 2. === ROUTE DEFINITIONS ===
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Public Products
    $r->addRoute('GET', '/', ['App\Controllers\ProductController', 'showAll']);
    $r->addRoute('GET', '/products', ['App\Controllers\ProductController', 'showAll']);
    $r->addRoute('GET', '/product/{id:\d+}', ['App\Controllers\ProductController', 'show']);

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
