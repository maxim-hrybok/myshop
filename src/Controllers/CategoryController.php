<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use Smarty\Smarty;

class CategoryController {
    private CategoryModel $categoryModel;
    private Smarty $smarty;

    public function __construct(\PDO $pdo, Smarty $smarty) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: /login');
            exit();
        }

        $this->categoryModel = new CategoryModel($pdo);
        $this->smarty = $smarty;
    }

    public function index() {
        $categories = $this->categoryModel->getAllCategories();
        $this->smarty->assign('categories', $categories);
        $this->smarty->assign('pageTitle', 'Manage Categories');
        $this->smarty->display('admin/categories/list.tpl');
    }

    public function create() {
        $this->smarty->assign('pageTitle', 'Create Category');
        $this->smarty->assign('category', null);
        $this->smarty->display('admin/categories/form.tpl');
    }

    public function store() {
        $name = trim($_POST['name'] ?? '');
        if (!empty($name)) {
            $this->categoryModel->createCategory($name);
        }
        header('Location: /admin/categories');
        exit();
    }

    public function edit($vars) {
        $id = (int)$vars['id'];
        $category = $this->categoryModel->findCategoryById($id);

        if (!$category) {
            header('Location: /admin/categories');
            exit();
        }

        $this->smarty->assign('pageTitle', 'Edit Category');
        $this->smarty->assign('category', $category);
        $this->smarty->display('admin/categories/form.tpl');
    }

    public function update($vars) {
        $id = (int)$vars['id'];
        $name = trim($_POST['name'] ?? '');

        if (!empty($name)) {
            $this->categoryModel->updateCategory($id, $name);
        }
        header('Location: /admin/categories');
        exit();
    }

    public function delete($vars) {
        $id = (int)$vars['id'];
        $this->categoryModel->deleteCategory($id);
        header('Location: /admin/categories');
        exit();
    }
}
?>