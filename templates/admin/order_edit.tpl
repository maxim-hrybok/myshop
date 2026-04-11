{include file='partials/header.tpl'}
<main style="padding: 20px; max-width: 800px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Edit Order #{$order.id}</h1>
        <a href="/admin/orders" class="button-primary" style="background: #6c757d; padding: 10px; color: white; text-decoration: none; border-radius: 5px;">&larr; Back to Orders</a>
    </div>
    
    <!-- 1. Order Meta Information -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px; display: flex; justify-content: space-between;">
        <div>
            <p style="margin: 0 0 10px 0;"><strong>Customer User ID:</strong> {$order.user_id}</p>
            <p style="margin: 0;"><strong>Date:</strong> {$order.created_at}</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0 0 10px 0; font-size: 1.2rem;"><strong>Total Paid:</strong> <span style="color: green;">${$order.total_price|number_format:2}</span></p>
            <p style="margin: 0;"><strong>Current Status:</strong> 
                <span style="text-transform: uppercase; font-weight: bold;
                    {if $order.status == 'pending'}color: orange;
                    {elseif $order.status == 'completed'}color: green;
                    {elseif $order.status == 'cancelled'}color: red;{/if}">
                    {$order.status}
                </span>
            </p>
        </div>
    </div>

    <!-- 2. Detailed Order Items Table -->
    <h2 style="margin-bottom: 10px; font-size: 1.5rem;">Purchased Items</h2>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background: #fff; margin-bottom: 30px;">
        <thead>
            <tr style="background: #f0f0f0;">
                <th style="width: 10%;">Image</th>
                <th style="width: 40%; text-align: left;">Product Name</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Price (Each)</th>
                <th style="width: 20%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            {if empty($orderItems)}
                <tr><td colspan="5" style="text-align: center; color: red;">Error: No items found for this order.</td></tr>
            {else}
                {foreach $orderItems as $item}
                <tr>
                    <td style="text-align: center;">
                        <img src="{$item.image_url|default:''|escape}" width="50" style="border-radius: 4px;" alt="Product Image">
                    </td>
                    <td><strong>{$item.name|escape}</strong><br><small style="color: #666;">Product ID: {$item.product_id}</small></td>
                    <td style="text-align: center; font-weight: bold;">x{$item.quantity}</td>
                    <td style="text-align: right;">${$item.price_at_purchase|number_format:2}</td>
                    {* Calculate the subtotal for this specific row *}
                    <td style="text-align: right; font-weight: bold;">${($item.quantity * $item.price_at_purchase)|number_format:2}</td>
                </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>

    <!-- 3. Status Update Form -->
    <div style="border-top: 2px dashed #ccc; padding-top: 20px;">
        <h2 style="margin-bottom: 15px; font-size: 1.5rem;">Update Order Status</h2>
        <form action="/admin/orders/update/{$order.id}" method="POST" style="display: flex; align-items: center; gap: 15px;">
            
            <div class="form-group" style="flex: 1; margin: 0;">
                <select name="status" id="status" style="width: 100%; padding: 12px; border-radius: 4px; border: 1px solid #ccc; font-size: 1.1rem; font-weight: bold;">
                    <option value="pending" {if $order.status == 'pending'}selected{/if}>Pending</option>
                    <option value="completed" {if $order.status == 'completed'}selected{/if}>Completed</option>
                    <option value="cancelled" {if $order.status == 'cancelled'}selected{/if}>Cancelled</option>
                </select>
            </div>

            <button type="submit" class="button-primary" style="padding: 12px 30px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; font-size: 1.1rem; font-weight: bold; transition: 0.2s;">
                Save Changes
            </button>
        </form>
    </div>

</main>
</body>
</html>