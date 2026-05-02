{include file='partials/header.tpl'}

<main class="main-container-sm">
    <h1>My Order History</h1>

    {if empty($orders)}
        <p>You haven't purchased anything yet.</p>
    {else}
        {foreach $orders as $order}
        <div class="order-card">
            <div class="order-header">
                <span><strong>Order #{$order.id}</strong></span>
                <span>Date: {$order.created_at}</span>
                <span>Status: <strong>{$order.status}</strong></span>
            </div>
            
            <table class="order-table">
                {foreach $order.items as $item}
                <tr>
                    <td>
                        {if $item.image_url|strpos:'/' === 0}
                            <img src="{$item.image_url|escape}" width="40" class="product-image" alt="Product Image">
                        {else}
                            <img src="/public/uploads/products/thumb_{$item.image_url|escape}" width="40" class="product-image" alt="Product Image">
                        {/if}
                    </td>
                    <td>{$item.name|escape}</td>
                    <td>x {$item.quantity}</td>
                    <td>${$item.price_at_purchase}</td>
                </tr>
                {/foreach}
            </table>
            
            <div class="order-total">
                <strong>Total Paid: ${$order.total_price}</strong>
            </div>
        </div>
        {/foreach}
    {/if}
</main>