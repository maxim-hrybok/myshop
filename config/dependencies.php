<?php 
use Smarty\Smarty;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use App\Config\ConfigService;



date_default_timezone_set('UTC');
return [
    // Bind ConfigService and pass the parsed $_ENV array securely
    ConfigService::class => function(ContainerInterface $c) {
        return new ConfigService($_ENV);
    },

    PDO::class => function(ContainerInterface $c) {
        // Fetch ConfigService from the DI container
        $config = $c->get(ConfigService::class);
        
        $host    = $config->get('db.host');
        $db      = $config->get('db.name');
        $user    = $config->get('db.user');
        $pass    = $config->get('db.pass');
        $charset = $config->get('db.charset');

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET time_zone = '+00:00'");
        
        return $pdo;
    },
    
    // Tell PHP-DI how to provide the Smarty instance
    Smarty::class => function(ContainerInterface $c) {
        $smarty = new Smarty();
        $smarty->setTemplateDir(__DIR__ . '/../templates');
        $smarty->setCompileDir(__DIR__ . '/../templates_c');
        $smarty->setCacheDir(__DIR__ . '/../cache');
        $smarty->force_compile = false;
        
        $smarty->registerPlugin('modifier', 'strpos', function($haystack, $needle) {
            return is_string($haystack) ? strpos($haystack, $needle) : false;
        });

        return $smarty;
    }
];