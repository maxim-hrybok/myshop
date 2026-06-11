{include file='partials/header.tpl'}
<main class="admin-main">
    <div class="admin-header">
        <h1>Manage Categories</h1>
        <a href="/admin/categories/create" class="btn-success">+ Add New Category</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 10%;">ID</th>
                <th style="width: 60%; text-align: left;">Name</th>
                <th style="width: 30%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            {if empty($categories)}
                <tr><td colspan="3" style="text-align: center;">No categories found.</td></tr>
            {else}
                {foreach $categories as $cat}
                <tr>
                    <td style="text-align: center;">{$cat.id}</td>
                    <td><strong>{$cat.name|escape}</strong></td>
                    <td style="text-align: center;">
                        <a href="/admin/categories/edit/{$cat.id}" class="admin-link">Edit</a>
                        <form action="/admin/categories/delete/{$cat.id}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure? Products in this category will NOT be deleted, but the category link will be removed.');">
                            <input type="hidden" name="csrf_token" value="{$csrf_token}">
                            <button type="submit" class="delete-link">Delete</button>
                        </form>
                    </td>
                </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        <a href="/admin" style="color: #666; text-decoration: none;">&larr; Back to Admin Dashboard</a>
    </div>
</main>
</body>
</html>