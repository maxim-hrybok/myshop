{* First, include the header template *}
{include file='partials/header.tpl'}

<main>
    <div class="product-grid">
        {* This is Smarty's foreach loop. It iterates over the $products array we assigned in the controller. *}
        {foreach $products as $product}
            {* For each product in the loop, include the card component. *}
            {* We pass the current product item from the loop into the card template. *}
            {include file='components/product_card.tpl' product=$product}
        {/foreach}
    </div>
</main>

{* You would include a footer here as well, e.g., {include file='partials/footer.tpl'} *}
</body>
</html>

