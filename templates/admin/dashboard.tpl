{include file='partials/header.tpl'}
<main class="admin-dashboard">
    <div class="admin-header">
        <h1>Admin Dashboard - Products</h1>
        <div class="admin-actions">
            <a href="/admin/comments" class="btn-category"">Moderate Comments</a>   
            <a href="/admin/orders" class="btn-danger">Manage Orders</a>
            <a href="/admin/categories" class="btn-category">Manage Categories</a>
            <a href="/admin/create" class="btn-success">+ Add New Product</a>
        </div>
    </div>

    <!-- Admin Filters -->
    <form action="/admin" method="GET" class="admin-filters">
        
        <input type="text" name="search" value="{$search|escape}" placeholder="Search by name..." class="admin-filter-input">
        
        <select name="status" class="admin-filter-select">
            <option value="all" {if $status == 'all'}selected{/if}>All Statuses</option>
            <option value="available" {if $status == 'available'}selected{/if}>Available</option>
            <option value="unavailable" {if $status == 'unavailable'}selected{/if}>Unavailable</option>
        </select>

        <div class="admin-categories-filter">
            <span style="font-size: 0.8rem; color: #666; margin-bottom: 3px;">Categories:</span>
            {foreach $allCategories as $cat}
                <label class="admin-category-label">
                    <input type="checkbox" name="categories[]" value="{$cat.id}" {if in_array($cat.id, $selectedCategories)}checked{/if}> {$cat.name|escape}
                </label>
            {/foreach}
        </div>

        <button type="submit" class="btn-primary">Filter</button>
        <a href="/admin" class="btn-secondary">Reset</a>
    </form>

    <!-- Products Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Categories</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {foreach $products as $p}
            <tr>
                <td>{$p.id}</td>
                <td>
                    {if $p.image_url|strpos:'/' === 0}
                        <img src="{$p.image_url|escape}" width="50" class="thumb-image">
                    {else}
                        <img src="/public/uploads/products/thumb_{$p.image_url|escape}" width="50" class="thumb-image">
                    {/if}
                </td>
                <td>{$p.name|escape}</td>
                <td>
                    {* FIX: Replaced count() with !empty() for Smarty 5 safety *}
                    {if !empty($p.categories)}
                        {foreach $p.categories as $c}
                            <span class="category-tag">{$c.name|escape}</span>
                        {/foreach}
                    {else}
                        <span class="text-muted-light">None</span>
                    {/if}
                </td>
                <td>${$p.price}</td>
                <td class="{if $p.stock < 5}stock-low{else}stock-good{/if}">{$p.stock}</td>
                <td>
                    {if $p.status == 'available'}
                        <span class="status-active">Active</span>
                    {else}
                        <span class="status-hidden">Hidden</span>
                    {/if}
                </td>
                <td>
                    <a href="/admin/edit/{$p.id}" class="admin-link">Edit</a>
                    <form action="/admin/delete/{$p.id}" method="POST" class="d-inline" onsubmit="return confirm('WARNING: This deletes the product entirely. Are you sure?');">
                        <button type="submit" class="delete-link">Delete</button>
                    </form>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>

    <!-- Admin Pagination -->
    {if $totalPages > 1}
        <div class="pagination">
            {for $i=1 to $totalPages}
                {* FIX: Using the pre-built queryString variable from the controller *}
                <a href="/admin?page={$i}&{$queryString}" class="page-link {if $i == $currentPage}active{/if}">
                    {$i}
                </a>
            {/for}
        </div>
    {/if}
</main>
</body>
</html>