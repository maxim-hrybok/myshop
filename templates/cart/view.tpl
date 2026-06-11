{include file='partials/header.tpl'}

<main class="main-container-sm">
    <h1>Shopping Cart</h1>

    {if isset($error)}
        <div class="alert-error">
            {$error}
        </div>
    {/if}

    {if empty($cartItems)}
        <p>Your cart is empty. <a href="/products">Go buy some steam keys!</a></p>
    {else}
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {foreach $cartItems as $item}
                <tr>
                    <td>{$item.name|escape}</td>
                    <td>${$item.price|number_format:2}</td>
                    <td>{$item.qty}</td>
                    <td>${$item.subtotal|number_format:2}</td>
                    <td>
                        <a href="/cart/remove/{$item.id}" class="text-danger">Remove</a>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>

        <div class="cart-total">
            <h2>Total: ${$total|number_format:2}</h2>
            
            {if isset($session.user_id)}
                <form action="/checkout" method="POST">
                    <input type="hidden" name="csrf_token" value="{$csrf_token}">
                    <button type="submit" class="btn-primary checkout-btn">
                        Checkout Now
                    </button>
                </form>
            {else}
                <p><a href="/login" class="login-link">Login</a> to Checkout</p>
            {/if}
        </div>
    {/if}
</main>