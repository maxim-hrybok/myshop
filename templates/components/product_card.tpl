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

   <div class="card__bottom">
        <div class="card__prices">
             {if $product.discount > 0}
                {* 1. price with discount check *}
                {$discountedPrice = $product.price * (1 - $product.discount / 100)}
                
                {* 2. new price with discount *}
                <div class="new-price">${$discountedPrice|number_format:2}</div>
                
                {* 3.old price *}
                <div class="old-price">${$product.price|number_format:2}</div>
            {else}
                {* no discount *}
                <div class="price">${$product.price|number_format:2}</div>
            {/if}
        </div>

        <a href="/product/{$product.id}" class="card__title">
            {$product.name|escape}
        </a>

        <!-- add to cart to product cards ))-->
        <form action="/cart/add/{$product.id}" method="POST">
             {* stock check  *}
             {if isset($product.stock) && $product.stock > 0}
                <button type="submit" class="card__add">Add to Cart</button>
             {else}
                 {if !isset($product.stock)} 
                    {* stock check*}
                    <button type="submit" class="card__add">Add to Cart</button>
                 {else}
                    <button type="button" class="card__add" style="background: grey; cursor: not-allowed;" disabled>Sold Out</button>
                 {/if}
             {/if}
        </form>
    </div>
</div>