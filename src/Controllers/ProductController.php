<?php

namespace App\Controllers;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CommentRepository;
use Smarty\Smarty;

class ProductController {
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private CommentRepository $commentRepository;
    private Smarty $smarty;
    

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository, CommentRepository $commentRepository, Smarty $smarty) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
        $this->smarty = $smarty;
    }

    public function showAll() {
        // 1. Read state from URL (GET)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $categoryIds = isset($_GET['categories']) && is_array($_GET['categories']) ? $_GET['categories'] :[];

        // 2. Fetch data ('available' check)
        $limit = 9; // 9 products per page looks for a grid
        $result = $this->productRepository->getFilteredProducts($page, $limit, $search, 'available', $categoryIds);
        $allCategories = $this->categoryRepository->getAllCategories();

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
        $product = $this->productRepository->getProductWithCalculations($id);

        // prevent users from directly viewing inactive products via URL
        if (!$product || $product['status'] !== 'available') {
            http_response_code(404);
            $this->smarty->assign('pageTitle', 'Product Not Found');
            $this->smarty->display('errors/404.tpl');
            return;
        }

        // Fetch approved comments
        $comments = $this->commentRepository->getApprovedCommentsForProduct($id);
        $this->smarty->assign("comments", $comments);

        if (isset($_SESSION['flash_message'])) {
            $this->smarty->assign('flash_message', $_SESSION['flash_message']);
            unset($_SESSION['flash_message']);
        }
        $this->smarty->assign("product", $product);
        $this->smarty->assign("pageTitle", $product['name']);
        $this->smarty->display('products/detail.tpl');
    }
    
     public function addComment(array $vars) {
        // if (session_status() === PHP_SESSION_NONE) session_start();
        
        // if (!isset($_SESSION['user_id'])) {
        //     header('Location: /login');
        //     exit();
        // } mooved to AuthMiddleware.php

        $productId = (int)$vars["id"];
        $userId = (int)$_SESSION['user_id'];
        $content = trim($_POST['content'] ?? '');

        if (!empty($content)) {
            $this->commentRepository->addComment($productId, $userId, $content);
            $_SESSION['flash_message'] = "Your comment has been submitted and is awaiting admin approval.";
        }

        header("Location: /product/" . $productId);
        exit();
    }
}
?>