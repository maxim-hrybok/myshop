<?php

namespace App\Controllers;

use Smarty\Smarty;

class PageController {
    private Smarty $smarty;

    public function __construct(Smarty $smarty) {
        $this->smarty = $smarty;
    }

    public function about() {
        $this->smarty->assign('pageTitle', 'About This Project');
        $this->smarty->display('pages/about.tpl');
    }
}