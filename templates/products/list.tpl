{* First, include the header template *}
{include file='partials/header.tpl'}

<main class="main-container flex-layout">
    
    <!-- Sidebar Filters -->
    <aside class="sidebar">
        <h3>Filters</h3>
        <form action="/products" method="GET">
            
            <div class="filter-section">
                <label class="filter-label">Search</label>
                <input type="text" name="search" value="{$search|escape}" placeholder="Search products..." class="filter-input">
            </div>

            <div class="filter-section">
                <label class="filter-label">Categories</label>
                {foreach $allCategories as $cat}
                    <label class="filter-checkbox">
                        <input type="checkbox" name="categories[]" value="{$cat.id}" {if in_array($cat.id, $selectedCategories)}checked{/if}>
                        {$cat.name|escape}
                    </label>
                {/foreach}
            </div>

            <button type="submit" class="btn-primary filter-submit">Apply Filters</button>
            <a href="/products" class="filter-clear">Clear Filters</a>
        </form>
    </aside>

    <!-- Product Grid & Pagination -->
    <section style="flex: 1;">
        {if empty($products)}
            <p class="no-products">No products found matching your criteria.</p>
        {else}
            <div class="product-grid">
                {foreach $products as $product}
                    {include file='components/product_card.tpl' product=$product}
                {/foreach}
            </div>
            
            <!-- Pagination Controls -->
            {if $totalPages > 1}
                <div class="pagination">
                    {for $i=1 to $totalPages}
                        <a href="?page={$i}&{$baseQuery}" class="page-link {if $i == $currentPage}active{/if}">
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