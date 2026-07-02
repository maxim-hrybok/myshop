<?php
namespace App\Controllers;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CommentRepository;
use App\Services\ProductService;
use Smarty\Smarty;
use Exception;

class AdminController {
    // 1. Declare properties with exact names
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private OrderRepository $orderRepository;
    private CommentRepository $commentRepository;
    private ProductService $productService;
    private Smarty $smarty;

    public function __construct(
        ProductRepository $productRepository, 
        CategoryRepository $categoryRepository, 
        OrderRepository $orderRepository, 
        CommentRepository $commentRepository, 
        ProductService $productService, 
        Smarty $smarty
    ) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: /login');
            exit();
        }

        // 2. Assign correctly
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->orderRepository = $orderRepository;
        $this->commentRepository = $commentRepository;
        $this->productService = $productService;
        $this->smarty = $smarty;
    }
    public function dashboard() {
        // Read GET parameters for validation and state
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        $categoryIds = isset($_GET['categories']) && is_array($_GET['categories']) ? $_GET['categories'] :[];

        // Fetch data
        $result = $this->productRepository->getFilteredProducts($page, 10, $search, $status, $categoryIds);
        $allCategories = $this->categoryRepository->getAllCategories();

        array_unshift($allCategories,['id' => '0', 'name' => 'None']);// Add a default option for uncategorized products


        // Build the URL query string in the Controller to ensure proper encoding and security
        $queryParams =[
            'search' => $search,
            'status' => $status,
            'categories' => $categoryIds
        ];
        $queryString = http_build_query($queryParams);

        $this->smarty->assign('products', $result['products']);
        $this->smarty->assign('totalPages', $result['total_pages']);
        $this->smarty->assign('currentPage', $result['current_page']);
        $this->smarty->assign('queryString', $queryString); 
        
        $this->smarty->assign('search', $search);
        $this->smarty->assign('status', $status);//available, unavailable
        $this->smarty->assign('selectedCategories', $categoryIds); //array
        $this->smarty->assign('allCategories', $allCategories);

        if (isset($_SESSION['flash_error'])) {
            $this->smarty->assign('error', $_SESSION['flash_error']);
            unset($_SESSION['flash_error']);
        }

        $this->smarty->assign('pageTitle', 'Admin Dashboard');
        $this->smarty->display('admin/dashboard.tpl');
    }

    public function create() {
        $this->smarty->assign('pageTitle', 'Add New Product');
        $this->smarty->assign('product', null);
        $this->smarty->assign('allCategories', $this->categoryRepository->getAllCategories());

        if (isset($_SESSION['flash_error'])) {
            $this->smarty->assign('error', $_SESSION['flash_error']);
            unset($_SESSION['flash_error']);
        }

        $this->smarty->display('admin/form.tpl');
    }

    public function edit($vars) {
        $id = (int)$vars['id'];
        $product = $this->productRepository->findProductById($id);

        if (!$product) {
            echo "Product not found.";
            return;
        }

        $productCategoryIds = array_column($product['categories'], 'id');
        $product['category_ids'] = $productCategoryIds;

        $this->smarty->assign('pageTitle', 'Edit Product');
        $this->smarty->assign('product', $product);
        $this->smarty->assign('allCategories', $this->categoryRepository->getAllCategories());

        if (isset($_SESSION['flash_error'])) {
            $this->smarty->assign('error', $_SESSION['flash_error']);
            unset($_SESSION['flash_error']);
        }

        $this->smarty->display('admin/form.tpl');
    }

    public function showOrders() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';

        //Fetch paginated and filtered data
        $result = $this->orderRepository->getFilteredOrders($page, 10, $status, $search);

        $queryParams = [
            'search' => $search,
            'status' => $status
        ];

        $queryString = http_build_query($queryParams);

        // give data to Smarty
        $this->smarty->assign('orders', $result['orders']);
        $this->smarty->assign('totalPages', $result['total_pages']);
        $this->smarty->assign('currentPage', $result['current_page']);
        $this->smarty->assign('queryString', $queryString); 
        
        // pass current filter values back to the view so the form remembers user's choice
        $this->smarty->assign('search', $search);
        $this->smarty->assign('status', $status);

        $this->smarty->assign('pageTitle', 'Manage Orders');
        $this->smarty->display('admin/orders.tpl');
    

    }

    public function editOrder($vars) {
        $id = (int)$vars['id'];
        $order = $this->orderRepository->getOrderById($id);

        if (!$order) {
            echo "Order not found.";
            return;
        }
        $orderItems = $this->orderRepository->getOrderItems($id);
        
        $this->smarty->assign('orderItems', $orderItems); 
        $this->smarty->assign('order', $order);
        $this->smarty->assign('pageTitle', 'Edit Order Status');
        $this->smarty->display('admin/order_edit.tpl');
    }

    public function updateOrderStatus($vars) {
        $id = (int)$vars['id'];
        $validStatuses = ['pending', 'completed', 'cancelled'];
        $newStatus = $_POST['status'] ?? '';

        if (in_array($newStatus, $validStatuses)) {
            $this->orderRepository->updateOrderStatus($id, $newStatus);
        }
        
        header('Location: /admin/orders');
        exit();
    }   
    
    public function deleteOrder($vars) {
        $id = (int)$vars['id'];
        $this->orderRepository->deleteOrder($id);

        header('Location: /admin/orders');
        exit();
    }

    public function showComments() {
        $pendingComments = $this->commentRepository->getPendingComments();
        
        $this->smarty->assign('comments', $pendingComments);
        $this->smarty->assign('pageTitle', 'Moderate Comments');
        $this->smarty->display('admin/comments.tpl');
    }

    public function approveComment($vars) {
        $id = (int)$vars['id'];
        $this->commentRepository->updateStatus($id, 'approved');
        header('Location: /admin/comments');
        exit();
    }

    public function deleteComment($vars) {
        $id = (int)$vars['id'];
        $this->commentRepository->deleteComment($id);
        header('Location: /admin/comments');
        exit();
    }
    // ... [dashboard(), create(), edit(), showOrders(), editOrder(),
    // updateOrderStatus(), deleteOrder(), showComments(),
    // approveComment(), deleteComment() 
    //REMAIN EXACTLY THE SAME (just using updated Repositories)] ...

    public function store() {
        try {
            $this->productService->storeProduct($_POST, $_FILES['image'] ?? null);
            header('Location: /admin');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /admin/create');
            exit();
        }
    }

    public function update($vars) {
        $id = (int)$vars['id'];
        try {
            $this->productService->updateProduct($id, $_POST, $_FILES['image'] ?? null);
            header('Location: /admin');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /admin/edit/' . $id);
            exit();
        }
    }

    public function delete($vars) {
        $this->productService->deleteProduct((int)$vars['id']);
        header('Location: /admin');
        exit();
    }
}