<?php
namespace App\Controllers;

use App\Services\CartService;
use Smarty\Smarty;

class CartController {
    private CartService $cartService;
    private Smarty $smarty;

    // Notice: We ask for CartService, not PDO
    public function __construct(CartService $cartService, Smarty $smarty) {
        $this->cartService = $cartService;
        $this->smarty = $smarty;
    }

    public function add($vars) {
        $id = (int)$vars['id'];
        $result = $this->cartService->add($id, 1);

        if (!$result['success']) {
            $_SESSION['flash_error'] = $result['message'];
        }
        
        header('Location: /cart');
        exit();
    }

    public function remove($vars) {
        $this->cartService->remove((int)$vars['id']);
        header('Location: /cart');
        exit();
    }

    public function view() {
        $data = $this->cartService->getCartDetails();
        
        $this->smarty->assign('cartItems', $data['items']);
        $this->smarty->assign('total', $data['total']);
        $this->smarty->assign('pageTitle', 'Shopping Cart');
        
        if (isset($_SESSION['flash_error'])) {
            $this->smarty->assign('error', $_SESSION['flash_error']);
            unset($_SESSION['flash_error']);
        }

        $this->smarty->display('cart/view.tpl');
    }

    public function checkout() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $result = $this->cartService->checkout($_SESSION['user_id']);

        if ($result['success']) {
            header('Location: /orders');
        } else {
            $_SESSION['flash_error'] = "Checkout Failed: " . $result['message'];
            header('Location: /cart');
        }
        exit();
    }
}