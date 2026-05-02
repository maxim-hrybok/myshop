<?php 
use Smarty\Smarty;
use DI\ContainerBuilder;

return [
    // Tell PHP-DI how to provide the PDO instance
    PDO::class => function() {
        require __DIR__ . '/database.php';
        return $pdo; // We return the $pdo variable created inside database.php
    },
    
    // Tell PHP-DI how to provide the Smarty instance
    Smarty::class => function() {
        return require __DIR__ . '/smarty.php';
    }
];