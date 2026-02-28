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

            {* would eventually be a form for adding to the cart *}
            <button class="product-detail__add-to-cart">Add to Cart</button>
        </div>
    </article>
    {else}
    <p>This product could not be found.</p>
    {/if}
</main>

{* would {include file='partials/footer.tpl'} *}
</body>
</html>