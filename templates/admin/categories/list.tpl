{include file='partials/header.tpl'}
<main style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Manage Categories</h1>
        <a href="/admin/categories/create" class="button-primary" style="background: green; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px;">+ Add New Category</a>
    </div>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background: #fff;">
        <thead>
            <tr style="background: #f0f0f0;">
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
                        <a href="/admin/categories/edit/{$cat.id}" style="color: blue; margin-right: 15px; text-decoration: none;">Edit</a>
                        <form action="/admin/categories/delete/{$cat.id}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure? Products in this category will NOT be deleted, but the category link will be removed.');">
                            <button type="submit" style="color: red; border: none; background: none; cursor: pointer; text-decoration: underline;">Delete</button>
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