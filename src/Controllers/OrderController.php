<?php
namespace App\Controllers;

use App\Models\OrderModel;
use Smarty\Smarty;

class OrderController {
    private OrderModel $orderModel;
    private Smarty $smarty;

    public function __construct(\PDO $pdo, Smarty $smarty) {
        // Auth check
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $this->orderModel = new OrderModel($pdo);
        $this->smarty = $smarty;
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $orders = $this->orderModel->getOrdersByUserId($userId);
        
        // Enrich orders with items for the view
        foreach ($orders as &$order) {
            $order['items'] = $this->orderModel->getOrderItems($order['id']);
        }

        $this->smarty->assign('orders', $orders);
        $this->smarty->assign('pageTitle', 'My Orders');
        $this->smarty->display('orders/list.tpl');
    }
}