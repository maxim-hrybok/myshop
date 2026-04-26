{include file='partials/header.tpl'}
<main style="padding: 20px; max-width: 800px; margin: 0 auto; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; margin-top: 2rem;">
    <h1>{if isset($product)}Edit Product{else}Add New Product{/if}</h1>

    {if isset($error)}
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
            {$error}
        </div>
    {/if}

<!-- CRITICAL: enctype="multipart/form-data" is required for file uploads -->
<form action="{if isset($product)}/admin/update/{$product.id}{else}/admin/store{/if}" method="POST" enctype="multipart/form-data">
    
    <!-- Retain existing image if editing -->
    {if isset($product)}
        <input type="hidden" name="existing_image_url" value="{$product.image_url|escape}">
    {/if}

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Left Column -->
        <div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Product Name:</label>
                <input type="text" name="name" value="{$product.name|default:''}" required style="width: 100%; padding: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Price ($):</label>
                <input type="number" step="0.01" name="price" value="{$product.price|default:''}" required style="width: 100%; padding: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Discount (%):</label>
                <input type="number" name="discount" value="{$product.discount|default:'0'}" style="width: 100%; padding: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Stock Quantity:</label>
                <input type="number" name="stock" value="{$product.stock|default:'0'}" required min="0" style="width: 100%; padding: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Visibility Status:</label>
                <label style="margin-right: 15px;">
                    <input type="radio" name="status" value="available" {if !isset($product) || $product.status == 'available'}checked{/if}> Available
                </label>
                <label>
                    <input type="radio" name="status" value="unavailable" {if isset($product) && $product.status == 'unavailable'}checked{/if}> Unavailable
                </label>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Categories:</label>
                <div style="border: 1px solid #ccc; padding: 10px; border-radius: 4px; max-height: 150px; overflow-y: auto;">
                    {foreach $allCategories as $cat}
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="checkbox" name="categories[]" value="{$cat.id}" 
                            {if isset($product) && isset($product.category_ids) && in_array($cat.id, $product.category_ids)}checked{/if}>
                            {$cat.name|escape}
                        </label>
                    {/foreach}
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: bold;">Product Image:</label>
                {if isset($product) && $product.image_url}
                    <div style="margin-bottom: 10px;">
                        <span style="font-size: 0.8rem; color: #666;">Current Image:</span><br>
                        {if $product.image_url|strpos:'/' === 0}
                            <img src="{$product.image_url|escape}" width="100" style="border: 1px solid #ccc; border-radius: 4px;">
                        {else}
                            <img src="/public/uploads/products/thumb_{$product.image_url|escape}" width="100" style="border: 1px solid #ccc; border-radius: 4px;">
                        {/if}
                    </div>
                {/if}
                <input type="file" name="image" accept="image/jpeg, image/png, image/webp" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <small style="color: #666;">Leave empty to keep existing image. Max 5MB (JPG, PNG, WebP).</small>
            </div>
        </div>
    </div>

    <!-- Full Width Row for Description -->
    <div class="form-group" style="margin-bottom: 20px;">
        <label style="font-weight: bold;">Description:</label>
        <textarea name="description" rows="5" required style="width: 100%; padding: 8px; box-sizing: border-box;">{$product.description|default:''}</textarea>
    </div>

    <button type="submit" class="button-primary" style="padding: 12px 25px; font-size: 1.1rem; cursor: pointer; width: 100%;">
        {if isset($product)}Save Changes{else}Create Product{/if}
    </button>
</form>
</main>
</body>
</html>