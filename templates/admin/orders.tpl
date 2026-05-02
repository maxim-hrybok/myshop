{include file='partials/header.tpl'}
<main class="admin-main">
    <div class="admin-header">
        <h1>Manage Orders</h1>
        <a href="/admin" class="btn-secondary">&larr; Back to Dashboard</a>
    </div>

    <!-- Filters Form -->
    <form method="GET" action="/admin/orders" class="filter-bar">
        
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
            <button type="submit" class="btn-primary" style="padding: 8px 15px;">Filter</button>
            <a href="/admin/orders" class="text-danger" style="margin-left: 10px; text-decoration: none;">Reset</a>
        </div>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
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
                    
                    <td style="text-align: center; font-weight: bold;" class="status-{$o.status}">
                        {$o.status|upper|escape}
                    </td>
                    
                    <td>{$o.created_at|escape}</td>
                    <td style="text-align: center;">
                        <a href="/admin/orders/edit/{$o.id}" class="admin-link">Edit</a>
                        <form action="/admin/orders/delete/{$o.id}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure? This action will permanently delete the order and its items.');">
                            <button type="submit" class="delete-link">Delete</button>
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
                <a href="/admin/orders?page={$i}&{$queryString}" class="page-link">{$i}</a>
            {/if}
        {/for}
    </div>
    {/if}

</main>
</body>
</html>