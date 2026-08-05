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

        <div class="cart-total" style="text-align: right; margin-top: 20px;">
            <h2>Total: ${$total|number_format:2}</h2>
            
            {if isset($session.user_id)}
                <!-- PayPal Button Container -->
                <div id="paypal-button-container" style="max-width: 300px; margin-left: auto;"></div>
                
                <!-- PayPal JS SDK -->
                <script src="https://www.paypal.com/sdk/js?client-id={$paypal_client_id}&currency=USD&disable-funding=credit,card"></script>
                
                <script>
                    // Pass CSRF token safely to JS
                    const csrfToken = "{$csrf_token}";

                    paypal.Buttons({
                        // 1. Call your backend to set up the transaction
                        createOrder: function() {
                            return fetch('/api/paypal/create-order', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-Token': csrfToken 
                                },
                                body: JSON.stringify({
                                    csrf_token: csrfToken 
                                })
                            }).then(function(res) {
                                return res.json();
                            }).then(function(orderData) {
                                return orderData.id;
                            });
                        },

                        // 2. Call your backend to finalize the transaction
                        onApprove: function(data, actions) {
                            return fetch('/api/paypal/capture-order', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-Token': csrfToken 
                                },
                                body: JSON.stringify({
                                    orderID: data.orderID,
                                    csrf_token: csrfToken
                                })
                            }).then(function(res) {
                                return res.json();
                            }).then(function(orderData) {
                                if (orderData.success) {
                                    // Redirect to orders page upon success
                                    window.location.href = "/orders";
                                } else {
                                    alert('Payment failed: ' + orderData.error);
                                }
                            });
                        }
                    }).render('#paypal-button-container');
                </script>
            {else}
                <p><a href="/login" class="login-link text-primary font-weight-bold">Login</a> to Checkout</p>
            {/if}
        </div>
    {/if}
</main>