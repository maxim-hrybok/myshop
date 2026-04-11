{include file='partials/header.tpl'}
<main style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Admin Dashboard - Products</h1>
        <div>
            <a href="/admin/orders" class="button-primary" style="background: #b82a17; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Manage Orders</a>
            <a href="/admin/categories" class="button-primary" style="background: #17a2b8; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Manage Categories</a>
            <a href="/admin/create" class="button-primary" style="background: green; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px;">+ Add New Product</a>
        </div>
    </div>

    <!-- Admin Filters -->
    <form action="/admin" method="GET" style="background: #f4f4f4; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center;">
        
        <input type="text" name="search" value="{$search|escape}" placeholder="Search by name..." style="padding: 8px; flex: 1;">
        
        <select name="status" style="padding: 8px;">
            <option value="all" {if $status == 'all'}selected{/if}>All Statuses</option>
            <option value="available" {if $status == 'available'}selected{/if}>Available</option>
            <option value="unavailable" {if $status == 'unavailable'}selected{/if}>Unavailable</option>
        </select>

        <div style="display: flex; flex-direction: column; max-height: 80px; overflow-y: auto; background: #fff; border: 1px solid #ccc; padding: 5px; min-width: 200px;">
            <span style="font-size: 0.8rem; color: #666; margin-bottom: 3px;">Categories:</span>
            {foreach $allCategories as $cat}
                <label style="font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="categories[]" value="{$cat.id}" {if in_array($cat.id, $selectedCategories)}checked{/if}> {$cat.name|escape}
                </label>
            {/foreach}
        </div>

        <button type="submit" style="padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer;">Filter</button>
        <a href="/admin" style="padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 3px;">Reset</a>
    </form>

    <!-- Products Table -->
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background: #fff;">
        <thead>
            <tr style="background: #f0f0f0;">
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
                <td><img src="{$p.image_url|default:'#'}" width="50"></td>
                <td>{$p.name|escape}</td>
                <td>
                    {* FIX: Replaced count() with !empty() for Smarty 5 safety *}
                    {if !empty($p.categories)}
                        {foreach $p.categories as $c}
                            <span style="background: #e2e3e5; padding: 2px 6px; border-radius: 10px; font-size: 0.8rem; display: inline-block; margin: 2px;">{$c.name|escape}</span>
                        {/foreach}
                    {else}
                        <span style="color: #999;">None</span>
                    {/if}
                </td>
                <td>${$p.price}</td>
                <td style="font-weight: bold; {if $p.stock < 5}color: red;{else}color: green;{/if}">{$p.stock}</td>
                <td>
                    {if $p.status == 'available'}
                        <span style="color: green; font-weight: bold;">Active</span>
                    {else}
                        <span style="color: red; font-weight: bold;">Hidden</span>
                    {/if}
                </td>
                <td>
                    <a href="/admin/edit/{$p.id}" style="color: blue; margin-right: 10px; text-decoration: none;">Edit</a>
                    <form action="/admin/delete/{$p.id}" method="POST" style="display:inline;" onsubmit="return confirm('WARNING: This deletes the product entirely. Are you sure?');">
                        <button type="submit" style="color: red; border: none; background: none; cursor: pointer; text-decoration: underline;">Delete</button>
                    </form>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>

    <!-- Admin Pagination -->
    {if $totalPages > 1}
        <div style="margin-top: 20px; text-align: center;">
            {for $i=1 to $totalPages}
                {* FIX: Using the pre-built queryString variable from the controller *}
                <a href="/admin?page={$i}&{$queryString}" style="display: inline-block; padding: 8px 12px; margin: 0 4px; border: 1px solid #ddd; text-decoration: none; {if $i == $currentPage}background: #007bff; color: white; border-color: #007bff;{else}background: #fff; color: #333;{/if}">
                    {$i}
                </a>
            {/for}
        </div>
    {/if}
</main>
</body>
</html>