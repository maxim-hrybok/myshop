{include file='partials/header.tpl'}

<main style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <h1>Admin Dashboard</h1>
    <div style="margin-bottom: 20px;">
        <a href="/admin/create" class="button-primary" style="background: green; padding: 10px; color: white; text-decoration: none; border-radius: 5px;">+ Add New Product</a>
    </div>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f0f0f0;">
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {foreach $products as $p}
            <tr>
                <td>{$p.id}</td>
                <td><img src="{$p.image_url|default:'#'}" width="50"></td>
                <td>{$p.name|escape}</td>
                <td>${$p.price}</td>
                <td style="font-weight: bold; {if $p.stock < 5}color: red;{else}color: green;{/if}">
                {$p.stock}
                </td>
                <td>
                    <a href="/admin/edit/{$p.id}" style="color: blue; margin-right: 10px;">Edit</a>
                    
                    {* Delete form (safer than a GET link) *}
                    <form action="/admin/delete/{$p.id}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                        <button type="submit" style="color: red; border: none; background: none; cursor: pointer; text-decoration: underline;">Delete</button>
                    </form>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</main>