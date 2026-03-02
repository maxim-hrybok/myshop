{include file='partials/header.tpl'}

<main style="padding: 20px; max-width: 600px; margin: 0 auto;">
    <h1>{if isset($product)}Edit Product{else}Add New Product{/if}</h1>

    {* Determine URL: if product exists, update, else store *}
    <form action="{if isset($product)}/admin/update/{$product.id}{else}/admin/store{/if}" method="POST">
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Product Name:</label>
            <input type="text" name="name" value="{$product.name|default:''}" required style="width: 100%; padding: 8px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Price ($):</label>
            <input type="number" step="0.01" name="price" value="{$product.price|default:''}" required style="width: 100%; padding: 8px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Discount (%):</label>
            <input type="number" name="discount" value="{$product.discount|default:'0'}" style="width: 100%; padding: 8px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Image URL:</label>
            <input type="text" name="image_url" value="{$product.image_url|default:''}" placeholder="https://..." style="width: 100%; padding: 8px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Description:</label>
            <textarea name="description" rows="5" required style="width: 100%; padding: 8px;">{$product.description|default:''}</textarea>
        </div>

        <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
            {if isset($product)}Update Product{else}Create Product{/if}
        </button>
    </form>
</main>