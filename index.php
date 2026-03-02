<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();


require_once __DIR__ . '/vendor/autoload.php';//+

use App\Controllers\ProductController;//+

// 1. Connect to the database. and samarty (config)
require_once __DIR__ . '/config/database.php';
$smarty = require_once __DIR__ . '/config/smarty.php';


// Make session data available to ALL Smarty templates
$smarty->assign('session', $_SESSION);

//$page=$_GET['page'];

//$requestUri = $_SERVER['REQUEST_URI'];
////$basePath = '/mysyte'; // The subdirectory of your project
//$route = ($requestUri);//str_replace($basePath, '', $requestUri);
//
//// A simple routing mechanism
//switch ($route) {
//    case '/':
//    case '/index.php':
//    case '/products':
//        // Route to the product list page
//        $controller = new ProductController($pdo, $smarty);
//        $controller->showAll();
//        break;
//
//    case 'about':
//        // Route to an about page (you create an AboutController)
//        echo "This is the About Us page."; // Placeholder
//        break;
//
//    default:
//        // Handle 404 Not Found
//        http_response_code(404);
//        echo "404 - Page Not Found";
//        break;
//}

// 2. === ROUTE DEFINITIONS ===
// Define all the "routes" or URLs your application will respond to.
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // A route for the homepage
    $r->addRoute('GET', '/', ['App\Controllers\ProductController', 'showAll']); // We'll need to create this controller

    // A route for showing all products
    $r->addRoute('GET', '/products', ['App\Controllers\ProductController', 'showAll']);

    // A route for showing a single product by its ID.
    // {id:\d+} is a placeholder that matches one or more digits.
    $r->addRoute('GET', '/product/{id:\d+}', ['App\Controllers\ProductController', 'show']);

    // auth routs
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'handleLogin']);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'showRegisterForm']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'handleRegister']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);

    // Admin routes
    // Dashboard
    $r->addRoute('GET', '/admin', ['App\Controllers\AdminController', 'dashboard']);

    // Create
    $r->addRoute('GET', '/admin/create', ['App\Controllers\AdminController', 'create']);
    $r->addRoute('POST', '/admin/store', ['App\Controllers\AdminController', 'store']);
    
    // Edit (Dynamic ID)
    $r->addRoute('GET', '/admin/edit/{id:\d+}', ['App\Controllers\AdminController', 'edit']);
    $r->addRoute('POST', '/admin/update/{id:\d+}', ['App\Controllers\AdminController', 'update']);
    
    // Delete (Usually POST for security)
    $r->addRoute('POST', '/admin/delete/{id:\d+}', ['App\Controllers\AdminController', 'delete']);
});

// 3. === DISPATCH THE REQUEST ===
// Get the current URL and HTTP method from the server.
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Let the router find the matching route.
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 4. === HANDLE THE ROUTE ===
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // Handle 404 Not Found error
        header("HTTP/1.0 404 Not Found");
        echo '404 - Page Not Found';

        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // Handle 405 Method Not Allowed error
        header("HTTP/1.0 405 Method Not Allowed");
        echo '405 - Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1]; // This is ['App\Controllers\ProductController', 'showAll']
        $vars = $routeInfo[2];    // This contains route parameters, e.g., ['id' => '123']

        // Create the controller and call the method, passing dependencies and URL parameters
        $controllerClass = $handler[0];
        $method = $handler[1];

        // This is a simple example. A DI container makes this part automatic and cleaner.
        $controller = new $controllerClass($pdo, $smarty);

        $controller->$method($vars);
        break;

}

?>

<?php //require_once __DIR__ . '/../components/footer.php'; ?>
