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
            <li><a href="/public/assets/info/changelog.pdf?v=2" target="_blank">Changelog</a></li>
            <li><a href="/about">About Us</a></li>

                {if isset($session.user_id)}
                    <li id="logreg"><a href="/logout">Logout</a></li>
                    <li id="logreg"><a href="/">Welcome, {$session.user_name|escape}!</a></li>
                    
                            {* Check if admin *}
                        {if isset($session.is_admin) && $session.is_admin == 1}
                            <li id="logreg"><a href="/admin" style="color: #ff6633;">Admin Panel</a></li>
                        {/if}
                    
                    <li id="logreg">
                        <a href="/cart" class="cart-link">
                            Cart
                            {if isset($cartItemCount) && $cartItemCount > 0}
                                <span class="cart-badge">{$cartItemCount}</span>
                            {/if}
                        </a>
                    </li>
                    <li id="logreg"><a href="/orders">Orders</a></li>
                    
                    
                {else}
                    <li id="logreg"><a href="/login">Login</a></li>
                    <li id="logreg"><a href="/register">Register</a></li>
                    
                    {* Guest users should also see the cart count *}
                    <li id="logreg">
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