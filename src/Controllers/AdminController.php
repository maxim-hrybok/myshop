<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use Smarty\Smarty;

class AdminController {
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private Smarty $smarty;

    public function __construct(\PDO $pdo, Smarty $smarty) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: /login');
            exit();
        }

        $this->productModel = new ProductModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->smarty = $smarty;
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

        $this->smarty->assign('pageTitle', 'Admin Dashboard');
        $this->smarty->display('admin/dashboard.tpl');
    }

    public function create() {
        $this->smarty->assign('pageTitle', 'Add New Product');
        $this->smarty->assign('product', null);
        $this->smarty->assign('allCategories', $this->categoryModel->getAllCategories());
        $this->smarty->display('admin/form.tpl');
    }

    public function store() {
        $name = $_POST['name'];
        $price = (float)$_POST['price'];
        $discount = (int)$_POST['discount'];
        $stock = (int)$_POST['stock'];
        $desc = $_POST['description'];
        $img = $_POST['image_url'];
        $status = $_POST['status'] ?? 'available';
        
        $categoryIds = isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] :[];

        $this->productModel->createProduct($name, $price, $discount, $stock, $desc, $img, $status, $categoryIds);
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
        $this->smarty->display('admin/form.tpl');
    }

    public function update($vars) {
        $id = (int)$vars['id'];
        
        $name = $_POST['name'];
        $price = (float)$_POST['price'];
        $discount = (int)$_POST['discount'];
        $stock = (int)$_POST['stock'];
        $desc = $_POST['description'];
        $img = $_POST['image_url'];
        $status = $_POST['status'] ?? 'available';

        $categoryIds = isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] :[];

        $this->productModel->updateProduct($id, $name, $price, $discount, $stock, $desc, $img, $status, $categoryIds);
        header('Location: /admin');
        exit();
    }

    public function delete($vars) {
        $id = (int)$vars['id'];
        $this->productModel->deleteProduct($id);
        header('Location: /admin');
        exit();
    }
}
?>