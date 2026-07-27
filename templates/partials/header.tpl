<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {* Use the $pageTitle variable we assigned in the controller *}
    <title>{$pageTitle|default:"Steam Card Shop"}</title>
    
    {* Google Fonts: Inter *}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {* These paths are correct assuming your web server's root is the 'public' folder *}
    <link rel="stylesheet" href="/public/assets/css/header.css"/>
    <link rel="stylesheet" href="/public/assets/css/card.css">
    <link rel="stylesheet" href="/public/assets/css/product-detail.css">
    <link rel="stylesheet" href="/public/assets/css/layout.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/products">All Products</a></li>
            {* <li><a href="/public/assets/info/changelog.pdf?v=2" target="_blank">Changelog</a></li> *}
            <li><a href="/about">About Us</a></li>

                {if isset($session.user_id)}
                    <li class="logreg"><a href="/logout">Logout</a></li>
                    <li><a href="/">Welcome, {$session.user_name|escape}!</a></li>
                    
                        {* Check if admin *}
                        {if isset($session.is_admin) && $session.is_admin == 1}
                            <li><a href="/admin" class="text-warning">Admin Panel</a></li>
                        {/if}
                    
                    <li>
                        <a href="/cart" class="cart-link">
                            Cart
                            {if isset($cartItemCount) && $cartItemCount > 0}
                                <span class="cart-badge">{$cartItemCount}</span>
                            {/if}
                        </a>
                    </li>
                    <li><a href="/orders">Orders</a></li>
                    
                    
                {else}
                    <li class="logreg"><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                    
                    {* Guest users should also see the cart count *}
                    <li>
                        <a href="/cart" class="cart-link">
                            Cart
                            {if isset($cartItemCount) && $cartItemCount > 0}
                                <span class="cart-badge">{$cartItemCount}</span>
                            {/if}
                        </a>
                    </li>
                {/if}

        </ul>
    </nav>
</header>