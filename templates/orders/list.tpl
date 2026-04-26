{include file='partials/header.tpl'}

<main style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">
    <h1>My Order History</h1>

    {if empty($orders)}
        <p>You haven't purchased anything yet.</p>
    {else}
        {foreach $orders as $order}
        <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; background: #f9f9f9; padding: 10px; margin: -15px -15px 15px -15px; border-bottom: 1px solid #ddd;">
                <span><strong>Order #{$order.id}</strong></span>
                <span>Date: {$order.created_at}</span>
                <span>Status: <strong>{$order.status}</strong></span>
            </div>
            
            <table style="width: 100%;">
                {foreach $order.items as $item}
                <tr>
                    <td style="width: 50px;">
                        {if $item.image_url|strpos:'/' === 0}
                            <img src="{$item.image_url|escape}" width="40" style="vertical-align: middle; border-radius: 4px;">
                        {else}
                            <img src="/public/uploads/products/thumb_{$item.image_url|escape}" width="40" style="vertical-align: middle; border-radius: 4px;">
                        {/if}
                    </td>
                    <td>{$item.name|escape}</td>
                    <td>x {$item.quantity}</td>
                    <td style="text-align: right;">${$item.price_at_purchase}</td>
                </tr>
                {/foreach}
            </table>
            
            <div style="text-align: right; margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 5px;">
                <strong>Total Paid: ${$order.total_price}</strong>
            </div>
        </div>
        {/foreach}
    {/if}
</main>