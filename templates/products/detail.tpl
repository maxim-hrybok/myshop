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
                <div class="text-success mb-2">
                    In Stock: {$product.stock}
                </div>
             <!-- Link or Form to Add to Cart -->
                <form action="/cart/add/{$product.id}" method="POST">
                    <button type="submit" class="product-detail__add-to-cart">Add to Cart</button>
                </form>
            {else}
                <div class="text-danger mb-2 font-large">
                    OUT OF STOCK
                </div>
                <button class="product-detail__add-to-cart btn-disabled">Sold Out</button>
            {/if}
        </div>
    </article>
    <!-- Comments Section -->
    <section class="comments-section">
        <h2>Customer Reviews & Comments</h2>

        {if isset($flash_message)}
            <div class="alert-success">
                {$flash_message}
            </div>
        {/if}

        <!-- Comment Form -->
        <div class="comment-form-box">
            {if isset($session.user_id)}
                <form action="/product/{$product.id}/comment" method="POST">
                    <div class="form-group">
                        <label for="content" class="form-label">Leave a comment (Emojis+):</label>
                        <textarea name="content" id="content" rows="4" required class="form-control mt-2"></textarea>
                    </div>
                    <button type="submit" class="btn-primary mt-2">Submit Comment</button>
                </form>
            {else}
                <p>Please <a href="/login" class="text-primary font-weight-bold">Login</a> to leave a comment.</p>
            {/if}
        </div>

        <!-- Comments List -->
        <div class="comments-list">
            {if empty($comments)}
                <p class="text-muted font-italic">No reviews yet. Be the first to comment!</p>
            {else}
                {foreach $comments as $comment}
                    <div class="comment-card">
                        <div class="comment-card-header">
                            <strong class="comment-author">{$comment.first_name|escape}</strong>
                            <span class="comment-date">{$comment.created_at|date_format:"%b %e, %Y %H:%M"}</span>
                        </div>
                        <!-- Use nl2br to preserve line breaks in the user's comment, and escape for security against XSS -->
                        <p class="comment-body">{$comment.content|escape|nl2br}</p>
                    </div>
                {/foreach}
            {/if}
        </div>
    </section>

    {else}
    <p>This product could not be found.</p>
    {/if}
</main>

{* would {include file='partials/footer.tpl'} *}
</body>
</html>