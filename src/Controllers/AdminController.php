<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Services\ImageService;
use Smarty\Smarty;

use App\Models\CommentModel;

class AdminController {
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private OrderModel $orderModel;
    private ImageService $imageService;
    private Smarty $smarty;

    private CommentModel $commentModel;

    public function __construct(\PDO $pdo, Smarty $smarty) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: /login');
            exit();
        }

        $this->productModel = new ProductModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->orderModel = new OrderModel($pdo);
        $this->imageService = new ImageService();
        $this->smarty = $smarty;

        $this->commentModel = new CommentModel($pdo);
    }

    public function dashboard() {
        // Read GET parameters for validation and state
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        $categoryIds = isset($_GET['categories']) && is_array($_GET['categories']) ? $_GET['categories'] :[];

        // Fetch data
        $result = $this->productModel->getFilteredProducts($page, 10, $search, $status, $categoryIds);
        $allCategories = $this->categoryModel->getAllCategories();

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
        $this->smarty->assign('allCategories', $this->categoryModel->getAllCategories());

        if (isset($_SESSION['flash_error'])) {
            $this->smarty->assign('error', $_SESSION['flash_error']);
            unset($_SESSION['flash_error']);
        }

        $this->smarty->display('admin/form.tpl');
    }

    public function store() {
        $name = trim($_POST['name'] ?? '');
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
        $discount = isset($_POST['discount']) ? (int)$_POST['discount'] : 0;
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
        $desc = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'available';
        $categoryIds = isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] :[];

        $imgFilename = '';
        if ($name === '' || $price <= 0 || $stock < 0 || $discount < 0 || $discount > 100) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_error'] = "Invalid product data provided. Name is required, and price must be greater than 0.";
            header('Location: /admin/create');
        exit();
    }
        try {
            // Handle new image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $imgFilename = $this->imageService->handleUpload($_FILES['image']);
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Image Upload Failed: " . $e->getMessage();
            header('Location: /admin/create');
            exit();
        }

        $this->productModel->createProduct($name, $price, $discount, $stock, $desc, $imgFilename, $status, $categoryIds);
        header('Location: /admin');
        exit();
    }

    public function edit($vars) {
        $id = (int)$vars['id'];
        $product = $this->productModel->findProductById($id);

        if (!$product) {
            echo "Product not found.";
            return;
        }

        $productCategoryIds = array_column($product['categories'], 'id');
        $product['category_ids'] = $productCategoryIds;

        $this->smarty->assign('pageTitle', 'Edit Product');
        $this->smarty->assign('product', $product);
        $this->smarty->assign('allCategories', $this->categoryModel->getAllCategories());

        if (isset($_SESSION['flash_error'])) {
            $this->smarty->assign('error', $_SESSION['flash_error']);
            unset($_SESSION['flash_error']);
        }

        $this->smarty->display('admin/form.tpl');
    }

    public function update($vars) {
        $id = (int)$vars['id'];
        $name = trim($_POST['name'] ?? '');
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
        $discount = isset($_POST['discount']) ? (int)$_POST['discount'] : 0;
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
        $desc = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'available';
        $categoryIds = isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] :[];

        // Retain existing image by default
        $imgFilename = $_POST['existing_image_url'] ?? '';

        if ($name === '' || $price <= 0 || $stock < 0 || $discount < 0 || $discount > 100) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_error'] = "Invalid product data provided. Name is required, and price must be greater than 0.";
            header('Location: /admin/edit/' . $id);
            exit();
        }

        try {
            // Process new image if uploaded
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $newImgFilename = $this->imageService->handleUpload($_FILES['image']);
                
                // If successful and we had an old image, delete the old ones from the server
                if (!empty($imgFilename) && strpos($imgFilename, '/') !== 0) {
                    $this->imageService->deleteImages($imgFilename);
                }
                $imgFilename = $newImgFilename;
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Image Upload Failed: " . $e->getMessage();
            header('Location: /admin/edit/' . $id);
            exit();
        }

        $this->productModel->updateProduct($id, $name, $price, $discount, $stock, $desc, $imgFilename, $status, $categoryIds);
        header('Location: /admin');
        exit();
    }

    public function delete($vars) {
        $id = (int)$vars['id'];
        
        // Fetch product to delete its images first
        $product = $this->productModel->findProductById($id);
        if ($product && !empty($product['image_url']) && strpos($product['image_url'], '/') !== 0) {
            $this->imageService->deleteImages($product['image_url']);
        }

        $this->productModel->deleteProduct($id);
        header('Location: /admin');
        exit();
    }

    public function showOrders() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';

        //Fetch paginated and filtered data
        $result = $this->orderModel->getFilteredOrders($page, 10, $status, $search);

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
        $order = $this->orderModel->getOrderById($id);

        if (!$order) {
            echo "Order not found.";
            return;
        }
        $orderItems = $this->orderModel->getOrderItems($id);
        
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
            $this->orderModel->updateOrderStatus($id, $newStatus);
        }
        
        header('Location: /admin/orders');
        exit();
    }   
    
    public function deleteOrder($vars) {
        $id = (int)$vars['id'];
        $this->orderModel->deleteOrder($id);

        header('Location: /admin/orders');
        exit();
    }

    public function showComments() {
        $pendingComments = $this->commentModel->getPendingComments();
        
        $this->smarty->assign('comments', $pendingComments);
        $this->smarty->assign('pageTitle', 'Moderate Comments');
        $this->smarty->display('admin/comments.tpl');
    }

    public function approveComment($vars) {
        $id = (int)$vars['id'];
        $this->commentModel->updateStatus($id, 'approved');
        header('Location: /admin/comments');
        exit();
    }

    public function deleteComment($vars) {
        $id = (int)$vars['id'];
        $this->commentModel->deleteComment($id);
        header('Location: /admin/comments');
        exit();
    }
    
}
?>