<?php

use FastRoute\RouteCollector;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

return function(RouteCollector $r) {
    // PUBLIC ROUTES
    $r->addRoute('GET', '/', ['App\Controllers\ProductController', 'showAll', []]);
    $r->addRoute('GET', '/products', ['App\Controllers\ProductController', 'showAll', []]);
    $r->addRoute('GET', '/product/{id:\d+}', ['App\Controllers\ProductController', 'show', []]);
    $r->addRoute('GET', '/about', ['App\Controllers\PageController', 'about', []]);

    // AUTH ROUTES
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLoginForm', []]);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'handleLogin', []]);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'showRegisterForm', []]);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'handleRegister', []]);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout', []]);

    // CART ROUTES
    $r->addRoute('GET', '/cart', ['App\Controllers\CartController', 'view', []]);
    $r->addRoute('POST', '/cart/add/{id:\d+}', ['App\Controllers\CartController', 'add', []]);
    $r->addRoute('GET', '/cart/add/{id:\d+}', ['App\Controllers\CartController', 'add', []]); 
    $r->addRoute('GET', '/cart/remove/{id:\d+}', ['App\Controllers\CartController', 'remove', []]);

    // PROTECTED ROUTES (Requires AuthMiddleware)
    $authMw = [AuthMiddleware::class];
    $r->addRoute('POST', '/product/{id:\d+}/comment', ['App\Controllers\ProductController', 'addComment', $authMw]);
    $r->addRoute('POST', '/checkout', ['App\Controllers\CartController', 'checkout', $authMw]);
    $r->addRoute('GET', '/orders', ['App\Controllers\OrderController', 'index', $authMw]);

    // ADMIN ROUTES (Requires AdminMiddleware)
    $adminMw = [AdminMiddleware::class];
    $r->addRoute('GET', '/admin', ['App\Controllers\AdminController', 'dashboard', $adminMw]);
    $r->addRoute('GET', '/admin/create', ['App\Controllers\AdminController', 'create', $adminMw]);
    $r->addRoute('POST', '/admin/store', ['App\Controllers\AdminController', 'store', $adminMw]);
    $r->addRoute('GET', '/admin/edit/{id:\d+}', ['App\Controllers\AdminController', 'edit', $adminMw]);
    $r->addRoute('POST', '/admin/update/{id:\d+}', ['App\Controllers\AdminController', 'update', $adminMw]);
    $r->addRoute('POST', '/admin/delete/{id:\d+}', ['App\Controllers\AdminController', 'delete', $adminMw]);
    
    // ADMIN CATEGORIES
    $r->addRoute('GET', '/admin/categories', ['App\Controllers\CategoryController', 'index', $adminMw]);
    $r->addRoute('GET', '/admin/categories/create', ['App\Controllers\CategoryController', 'create', $adminMw]);
    $r->addRoute('POST', '/admin/categories/store', ['App\Controllers\CategoryController', 'store', $adminMw]);
    $r->addRoute('GET', '/admin/categories/edit/{id:\d+}', ['App\Controllers\CategoryController', 'edit', $adminMw]);
    $r->addRoute('POST', '/admin/categories/update/{id:\d+}', ['App\Controllers\CategoryController', 'update', $adminMw]);
    $r->addRoute('POST', '/admin/categories/delete/{id:\d+}', ['App\Controllers\CategoryController', 'delete', $adminMw]);
    
    // ADMIN COMMENTS
    $r->addRoute('GET', '/admin/comments', ['App\Controllers\AdminController', 'showComments', $adminMw]);
    $r->addRoute('POST', '/admin/comments/approve/{id:\d+}', ['App\Controllers\AdminController', 'approveComment', $adminMw]);
    $r->addRoute('POST', '/admin/comments/delete/{id:\d+}', ['App\Controllers\AdminController', 'deleteComment', $adminMw]);

    // ADMIN ORDERS
    $r->addRoute('GET', '/admin/orders', ['App\Controllers\AdminController', 'showOrders', $adminMw]);
    $r->addRoute('GET', '/admin/orders/edit/{id:\d+}', ['App\Controllers\AdminController', 'editOrder', $adminMw]);
    $r->addRoute('POST', '/admin/orders/update/{id:\d+}', ['App\Controllers\AdminController', 'updateOrderStatus', $adminMw]);
    $r->addRoute('POST', '/admin/orders/delete/{id:\d+}', ['App\Controllers\AdminController', 'deleteOrder', $adminMw]);

    // API ROUTES (Requires AuthMiddleware)
    $r->addRoute('POST', '/api/paypal/create-order', ['App\Controllers\Api\PayPalController', 'createOrder', $authMw]);
    $r->addRoute('POST', '/api/paypal/capture-order', ['App\Controllers\Api\PayPalController', 'captureOrder', $authMw]);

    // WEBHOOK ROUTE (No Auth, CSRF already excluded in CsrfMiddleware)
    $r->addRoute('POST', '/api/paypal/webhook', ['App\Controllers\Api\PayPalController', 'webhook', []]);
};