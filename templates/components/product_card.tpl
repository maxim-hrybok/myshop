<div class="card">
    <!-- Top Part -->
    <div class="card__top">
        <a href="/product/{$product.id}" class="card__image">
            {* Use the 'escape' modifier for security, just like htmlspecialchars() *}
            <img src="{$product.image_url|escape}" alt="{$product.name|escape}">
        </a>

        {* Smarty's if-statement syntax *}
        {if $product.discount > 0}
            <div class="card__label">-{$product.discount}%</div>
        {/if}
    </div>

    <!-- Bottom Part -->
    <div class="card__bottom">
        <div class="card__prices">
             {if $product.discount > 0}
                {* We can do calculations right in the template and use modifiers for formatting *}
                {$discountedPrice = $product.price * (1 - $product.discount / 100)}
                <p class="new-price">${$discountedPrice|number_format:2}</p>
            {else}
                <p class="price">${$product.price|number_format:2}</p>
            {/if}
        </div>

        <a href="/product/{$product.id}" class="card__title">
            {$product.name|escape}
        </a>

        <button class="card__add">Add to Cart</button>
    </div>
</div>