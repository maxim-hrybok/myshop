{include file='partials/header.tpl'}

<main class="product-detail-container">
    {* Check if the product variable exists, just in case *}
    {if isset($product)}
    <article class="product-detail">
        <div class="product-detail__image-wrapper">
            {if $product.image_url|strpos:'/' === 0}
                <img src="{$product.image_url|escape}" alt="{$product.name|escape}">
            {else}
                <img src="/public/uploads/products/large_{$product.image_url|escape}" alt="{$product.name|escape}">
            {/if}
        </div>

        <div class="product-detail__info">
            <h1 class="product-detail__title">{$product.name|escape}</h1>

            {* !!add a description field to your database  *}
            <p class="product-detail__description">
                {$product.description|escape}
            </p>

            <div class="product-detail__pricing">
                {if $product.discount > 0}
                {$discountedPrice = $product.price * (1 - $product.discount / 100)}
                <span class="new-price">${$discountedPrice|number_format:2}</span>
                <span class="old-price"><del>${$product.price|number_format:2}</del></span>
                {else}
                <span class="price">${$product.price|number_format:2}</span>
                {/if}
            </div>
            {if $product.stock > 0}
                <div class="text-success" style="margin-bottom: 10px;">
                    In Stock: {$product.stock}
                </div>
             <!-- Link or Form to Add to Cart -->
                <form action="/cart/add/{$product.id}" method="POST">
                    <button type="submit" class="product-detail__add-to-cart">Add to Cart</button>
                </form>
            {else}
                <div class="text-danger" style="margin-bottom: 10px; font-size: 1.2rem;">
                    OUT OF STOCK
                </div>
                <button class="product-detail__add-to-cart btn-disabled">Sold Out</button>
            {/if}
        </div>
    </article>
    {else}
    <p>This product could not be found.</p>
    {/if}
</main>

{* would {include file='partials/footer.tpl'} *}
</body>
</html>