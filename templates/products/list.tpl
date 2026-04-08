{* First, include the header template *}
{include file='partials/header.tpl'}

<main style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem; display: flex; gap: 20px;">
    
    <!-- Sidebar Filters -->
    <aside style="width: 250px; background: #f9f9f9; padding: 20px; border-radius: 8px; align-self: flex-start;">
        <h3 style="margin-top: 0;">Filters</h3>
        <form action="/products" method="GET">
            
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Search</label>
                <input type="text" name="search" value="{$search|escape}" placeholder="Search products..." style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Categories</label>
                {foreach $allCategories as $cat}
                    <label style="display: block; margin-bottom: 5px; cursor: pointer;">
                        <input type="checkbox" name="categories[]" value="{$cat.id}" {if in_array($cat.id, $selectedCategories)}checked{/if}>
                        {$cat.name|escape}
                    </label>
                {/foreach}
            </div>

            <button type="submit" class="button-primary" style="width: 100%; padding: 10px;">Apply Filters</button>
            <a href="/products" style="display: block; text-align: center; margin-top: 10px; color: #666; font-size: 0.9rem; text-decoration: none;">Clear Filters</a>
        </form>
    </aside>

    <!-- Product Grid & Pagination -->
    <section style="flex: 1;">
        {if empty($products)}
            <p style="text-align: center; font-size: 1.2rem; color: #666;">No products found matching your criteria.</p>
        {else}
            <div class="product-grid">
                {foreach $products as $product}
                    {include file='components/product_card.tpl' product=$product}
                {/foreach}
            </div>
            
            <!-- Pagination Controls -->
            {if $totalPages > 1}
                <div style="margin-top: 30px; text-align: center;">
                    {for $i=1 to $totalPages}
                        <a href="?page={$i}&{$baseQuery}" style="display: inline-block; padding: 8px 12px; margin: 0 4px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; {if $i == $currentPage}background: #007bff; color: white; border-color: #007bff;{else}background: #fff; color: #333;{/if}">
                            {$i}
                        </a>
                    {/for}
                </div>
            {/if}
        {/if}
    </section>

</main>
</body>
</html>