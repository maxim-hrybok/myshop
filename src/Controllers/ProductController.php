<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use Smarty\Smarty;

class ProductController {
    private ProductModel $model; 
    private CategoryModel $categoryModel;
    private Smarty $smarty;

    public function __construct(ProductModel $model, CategoryModel $categoryModel, Smarty $smarty) {
        $this->model = $model;
        $this->categoryModel = $categoryModel;
        $this->smarty = $smarty;
    }

    public function showAll() {
        // 1. Read state from URL (GET)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $categoryIds = isset($_GET['categories']) && is_array($_GET['categories']) ? $_GET['categories'] :[];

        // 2. Fetch data ('available' check)
        $limit = 9; // 9 products per page looks for a grid
        $result = $this->model->getFilteredProducts($page, $limit, $search, 'available', $categoryIds);
        $allCategories = $this->categoryModel->getAllCategories();

        array_unshift($allCategories, ['id' => '0', 'name' => 'None']);// Add a default option for uncategorized products

        // 3. Build a query string for pagination links (save search and category filters in the URL)
        $queryParams =[
            'search' => $search,
            'categories' => $categoryIds
        ];
        $baseQuery = http_build_query($queryParams);

        // 4. Give to Smarty
        $this->smarty->assign("products", $result['products']);//the products for the current page
        $this->smarty->assign("totalPages", $result['total_pages']);
        $this->smarty->assign("currentPage", $result['current_page']);
        $this->smarty->assign("baseQuery", $baseQuery);
        
        $this->smarty->assign("search", $search);
        $this->smarty->assign("selectedCategories", $categoryIds);
        $this->smarty->assign("allCategories", $allCategories);
        $this->smarty->assign("pageTitle", "All Products");
        
        $this->smarty->display('products/list.tpl');//here the view
    }

    public function show(array $vars) {
        $id = (int)$vars["id"];
        $product = $this->model->getProductWithCalculations($id);

        // prevent users from directly viewing inactive products via URL
        if (!$product || $product['status'] !== 'available') {
            http_response_code(404);
            $this->smarty->assign('pageTitle', 'Product Not Found');
            $this->smarty->display('errors/404.tpl');
            return;
        }

        $this->smarty->assign("product", $product);
        $this->smarty->assign("pageTitle", $product['name']);
        $this->smarty->display('products/detail.tpl');
    }
}
?>