{include file='partials/header.tpl'}
<main style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Manage Orders</h1>
        <a href="/admin" class="button-primary" style="background: #6c757d; padding: 10px; color: white; text-decoration: none; border-radius: 5px;">&larr; Back to Dashboard</a>
    </div>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background: #fff;">
        <thead>
            <tr style="background: #f0f0f0;">
                <th style="width: 10%;">Order ID</th>
                <th style="width: 10%;">User ID</th>
                <th style="width: 25%; text-align: left;">Total Price</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 20%;">Created At</th>
                <th style="width: 20%;">Actions</th> <!-- Fixed Missing Header -->
            </tr>
        </thead>
        <tbody>
            {if empty($orders)}
                <tr><td colspan="6" style="text-align: center;">No orders found.</td></tr>
            {else}
                {foreach $orders as $o}
                <tr>
                    <td style="text-align: center;">{$o.id}</td>
                    <td style="text-align: center;"><strong>{$o.user_id|escape}</strong></td>
                    <td><strong>${$o.total_price|number_format:2}</strong></td>
                    
                    {* Visual color coding for status *}
                    <td style="text-align: center; font-weight: bold; 
                        {if $o.status == 'pending'}color: orange;
                        {elseif $o.status == 'completed'}color: green;
                        {elseif $o.status == 'cancelled'}color: red;{/if}">
                        {$o.status|upper|escape}
                    </td>
                    
                    <td>{$o.created_at|escape}</td>
                    <td style="text-align: center;">
                        <a href="/admin/orders/edit/{$o.id}" style="color: blue; margin-right: 15px; text-decoration: none; font-weight: bold;">Edit</a>
                        <form action="/admin/orders/delete/{$o.id}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure? This action will permanently delete the order and its items.');">
                            <button type="submit" style="color: red; border: none; background: none; cursor: pointer; text-decoration: underline; font-weight: bold;">Delete</button>
                        </form>
                    </td>
                </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
</main>
</body>
</html>