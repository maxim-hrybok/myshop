{include file='partials/header.tpl'}

<main style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">
    <h1>Shopping Cart</h1>

    {if isset($error)}
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
            {$error}
        </div>
    {/if}

    {if empty($cartItems)}
        <p>Your cart is empty. <a href="/products">Go buy some steam keys!</a></p>
    {else}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd; text-align: left;">
                    <th style="padding: 10px;">Product</th>
                    <th style="padding: 10px;">Price</th>
                    <th style="padding: 10px;">Qty</th>
                    <th style="padding: 10px;">Subtotal</th>
                    <th style="padding: 10px;">Action</th>
                </tr>
            </thead>
            <tbody>
                {foreach $cartItems as $item}
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">{$item.name|escape}</td>
                    <td style="padding: 10px;">${$item.price|number_format:2}</td>
                    <td style="padding: 10px;">{$item.qty}</td>
                    <td style="padding: 10px;">${$item.subtotal|number_format:2}</td>
                    <td style="padding: 10px;">
                        <a href="/cart/remove/{$item.id}" style="color: red;">Remove</a>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>

        <div style="text-align: right;">
            <h2>Total: ${$total|number_format:2}</h2>
            
            {if isset($session.user_id)}
                <form action="/checkout" method="POST">
                    <button type="submit" class="button-primary" style="padding: 15px 30px; font-size: 1.2rem; cursor: pointer;">
                        Checkout Now
                    </button>
                </form>
            {else}
                <p><a href="/login" style="color: blue;">Login</a> to Checkout</p>
            {/if}
        </div>
    {/if}
</main>