{include file='partials/header.tpl'}

<main class="product-detail-container">
    {* Check if the product variable exists, just in case *}
    {if isset($product)}
    <article class="product-detail">
        <div class="product-detail__image-wrapper">
            <img src="{$product.image_url|escape}" alt="{$product.name|escape}">
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
                <div style="color: green; font-weight: bold; margin-bottom: 10px;">
                    In Stock: {$product.stock}
                </div>
             <!-- Link or Form to Add to Cart -->
                <form action="/cart/add/{$product.id}" method="POST">
                    <button type="submit" class="product-detail__add-to-cart">Add to Cart</button>
                </form>
            {else}
                <div style="color: red; font-weight: bold; margin-bottom: 10px; font-size: 1.2rem;">
                    OUT OF STOCK
                </div>
                <button class="product-detail__add-to-cart" disabled style="background: #ccc; cursor: not-allowed;">Sold Out</button>
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