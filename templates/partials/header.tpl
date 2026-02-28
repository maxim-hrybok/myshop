<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    {* Use the $pageTitle variable we assigned in the controller *}
    <title>{$pageTitle|default:"Steam Card Shop"}</title>
    {* These paths are correct assuming your web server's root is the 'public' folder *}
    <link rel="stylesheet" href="/public/assets/css/header.css"/>
    <link rel="stylesheet" href="/public/assets/css/card.css">
    <link rel="stylesheet" href="/public/assets/css/product-detail.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/products">All Products</a></li>
            <li><a href="/about">About Us</a></li>

                {if isset($session.user_id)}
                    <li id="logreg"><a href="/">Welcome, {$session.user_name|escape}!</a></li>
                    <li id="logreg"><a href="/logout">Logout</a></li>
                {else}
                    <li id="logreg"><a href="/login">Login</a></li>
                    <li id="logreg"> <a href="/register">Register</a></li>
                {/if}

        </ul>
    </nav>
</header>