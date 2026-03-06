<?php

namespace App\Controllers;

use App\Models\ProductModel;
use Smarty\Smarty;

class AdminController {
    private ProductModel $productModel;
    private Smarty $smarty;

    public function __construct(\PDO $pdo, Smarty $smarty) {

        if (session_status() === PHP_SESSION_NONE) session_start();


        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: /login');
            exit();
        }

        $this->productModel = new ProductModel($pdo);
        $this->smarty = $smarty;

    }

    public function dashboard() {
        $products = $this->productModel->getAllProducts();
        $this->smarty->assign('products', $products);
        $this->smarty->assign('pageTitle', 'Admin Dashboard');
        $this->smarty->display('admin/dashboard.tpl');
    }

         /**
         * Show the "create product" form.
         */
    public function create(){
        $this->smarty->assign('pageTitle', 'Add New Product');

        $this->smarty->assign('product', null); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! No existing product data for the create form ########### update undeerstanding of this line @@@@@@@
        $this->smarty->display('admin/form.tpl');
    }

        /**
        * Handle the form submission to create a new product in the database.
        */
    public function store() {
        $name = $_POST['name'];
        $price = (float)$_POST['price'];
        $discount = (int)$_POST['discount'];
        $desc = $_POST['description'];
        $img = $_POST['image_url'];

        $this->productModel->createProduct($name, $price, $discount, $desc, $img);
        header('Location: /admin');
        exit();
    }

        /**
        * Show the "edit product" form for a specific product ID.
        * @param array $vars The route variables, including 'id' for the product to edit.
        */
    public function edit($vars){
        $id = (int)$vars['id'];
        $product = $this->productModel->findProductById($id);

        if (!$product) {
            echo "Product not found.";
            return;
        }

        $this->smarty->assign('pageTitle', 'Edit Product');
        $this->smarty->assign('product', $product);
        $this->smarty->display('admin/form.tpl');
    }

        /**
        * Handle the "Update" POST request
         */
    public function update($vars) {
        $id = (int)$vars['id'];
        
        $name = $_POST['name'];
        $price = (float)$_POST['price'];
        $discount = (int)$_POST['discount'];
        $desc = $_POST['description'];
        $img = $_POST['image_url'];

        $this->productModel->updateProduct($id, $name, $price, $discount, $desc, $img);
        header('Location: /admin');
        exit();
    }

        /**
        * Handle the "Delete" POST request
        */
    public function delete($vars) {
        $id = (int)$vars['id'];
        $this->productModel->deleteProduct($id);
        header('Location: /admin');
        exit();
    }
}