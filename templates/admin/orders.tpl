{include file='partials/header.tpl'}
<main style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Manage Orders</h1>
        <a href="/admin" class="button-primary" style="background: #6c757d; padding: 10px; color: white; text-decoration: none; border-radius: 5px;">&larr; Back to Dashboard</a>
    </div>

    <!-- Filters Form -->
    <form method="GET" action="/admin/orders" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; border: 1px solid #ddd;">
        
        <div style="flex: 1;">
            <label for="search" style="font-weight: bold; margin-right: 10px;">Search (Order ID or User ID):</label>
            <input type="text" name="search" id="search" value="{$search|escape}" placeholder="e.g. 15" style="padding: 8px; width: 100%; max-width: 200px;">
        </div>

        <div>
            <label for="status" style="font-weight: bold; margin-right: 10px;">Status:</label>
            <select name="status" id="status" style="padding: 8px;">
                <option value="all" {if $status == 'all'}selected{/if}>All</option>
                <option value="pending" {if $status == 'pending'}selected{/if}>Pending</option>
                <option value="completed" {if $status == 'completed'}selected{/if}>Completed</option>
                <option value="cancelled" {if $status == 'cancelled'}selected{/if}>Cancelled</option>
            </select>
        </div>

        <div>
            <button type="submit" class="button-primary" style="padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">Filter</button>
            <a href="/admin/orders" style="margin-left: 10px; color: #dc3545; text-decoration: none;">Reset</a>
        </div>
    </form>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background: #fff;">
        <thead>
            <tr style="background: #f0f0f0;">
                <th style="width: 10%;">Order ID</th>
                <th style="width: 10%;">User ID</th>
                <th style="width: 25%; text-align: left;">Total Price</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 20%;">Created At</th>
                <th style="width: 20%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            {if empty($orders)}
                <tr><td colspan="6" style="text-align: center; padding: 20px;">No orders found matching your criteria.</td></tr>
            {else}
                {foreach $orders as $o}
                <tr>
                    <td style="text-align: center;">{$o.id}</td>
                    <td style="text-align: center;"><strong>{$o.user_id|escape}</strong></td>
                    <td><strong>${$o.total_price|number_format:2}</strong></td>
                    
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

    <!-- Pagination -->
    {if $totalPages > 1}
    <div style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
        {for $i=1 to $totalPages}
            {if $i == $currentPage}
                <span style="padding: 8px 12px; background: #007bff; color: white; border-radius: 4px; font-weight: bold;">{$i}</span>
            {else}
                <a href="/admin/orders?page={$i}&{$queryString}" style="padding: 8px 12px; background: #f0f0f0; color: #333; text-decoration: none; border-radius: 4px; border: 1px solid #ccc;">{$i}</a>
            {/if}
        {/for}
    </div>
    {/if}

</main>
</body>
</html>