<?php
//require_once __DIR__ . '/../Models/ProductModel.php';
namespace App\Controllers;

use App\Models\ProductModel;


use Smarty\Smarty;
class ProductController {
    private ProductModel $model; //
    private Smarty $smarty;
    public function __construct(\PDO $pdo, $smarty) {
        $this->model = new ProductModel($pdo);
        $this->smarty = $smarty;//store smarty object
    }

    // method for show render view with smarty
    public function showAll() {
        // 1. Get data from the model
        $products = $this->model->getAllProducts();
        // 2. "Assign" the data to Smarty. This makes the $products variable
        //    available inside our Smarty template.
        $this->smarty->assign("products", $products);
        $this->smarty->assign("pageTitle", "All Steam Cards");//example for another variable
        //include __DIR__ . '/../views/products/list.php';
        $this->smarty->display('products/list.tpl');
    }
    public function show(array $vars) {
        // 1. Get a single product's data from the model using the ID.
        $id = (int)$vars["id"];
        $product = $this->model->findProductById($id);

        // 2. Handle the "Not Found" case.

        if (!$product) {
            http_response_code(404);
            // We can reuse Smarty to display a user-friendly error page.
            $this->smarty->assign('pageTitle', 'Product Not Found');
            $this->smarty->display('errors/404.tpl');
            return; // Stop execution
        }

        // 3. "Assign" the data to Smarty.
        $this->smarty->assign("product", $product);
        $this->smarty->assign("pageTitle", $product['name']); // Set the page title to the product's name

        // 4. Display the new template for the product detail view.
        $this->smarty->display('products/detail.tpl');
    }
}
?>
