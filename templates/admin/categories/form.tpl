{include file='partials/header.tpl'}
<main style="padding: 20px; max-width: 500px; margin: 2rem auto; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px;">
    <h1>{if isset($category)}Edit Category{else}Add Category{/if}</h1>

    <form action="{if isset($category)}/admin/categories/update/{$category.id}{else}/admin/categories/store{/if}" method="POST">
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Category Name:</label>
            <input type="text" name="name" value="{$category.name|default:''}" required style="width: 100%; padding: 10px; box-sizing: border-box;" placeholder="e.g. Action Games, Hardware, etc.">
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="button-primary" style="flex: 1; padding: 10px; background: #007bff; border: none; color: white; cursor: pointer;">
                {if isset($category)}Update{else}Create{/if}
            </button>
            <a href="/admin/categories" style="flex: 1; padding: 10px; background: #ccc; color: #333; text-align: center; text-decoration: none; border-radius: 3px;">Cancel</a>
        </div>
    </form>
</main>
</body>
</html>