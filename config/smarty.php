<?php

// Make sure Composer's autoloader is included. This is crucial.
use Smarty\Smarty;

require_once __DIR__ . '/../vendor/autoload.php';

// Create a new instance of the Smarty class.
$smarty = new Smarty();

// Define the directory paths for Smarty.
// __DIR__ provides the absolute path of the current directory (config).
$smarty->setTemplateDir(__DIR__ . '/../templates');
$smarty->setCompileDir(__DIR__ . '/../templates_c');
$smarty->setCacheDir(__DIR__ . '/../cache');
// You can also set a config directory if you plan to use Smarty config files.
// $smarty->setConfigDir(__DIR__ . '/../config');

// This line is useful for development. It forces Smarty to recompile
// templates every time, so you see changes immediately.
// Turn this off in a production environment for better performance.
$smarty->force_compile = false;

// Return the fully configured object so it can be used elsewhere.
return $smarty;