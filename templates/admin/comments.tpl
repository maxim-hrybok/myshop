{include file='partials/header.tpl'}
<main class="main-container">
    <div class="flex-between">
        <h1>Moderate Pending Comments</h1>
        <a href="/admin" class="btn-secondary">&larr; Back to Dashboard</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th class="w-15">Date</th>
                <th class="w-15">User</th>
                <th class="w-15">Product</th>
                <th class="w-40">Comment (Emojis ON)</th>
                <th class="w-15 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            {if empty($comments)}
                <tr><td colspan="5" class="text-center p-20">No pending comments to review!</td></tr>
            {else}
                {foreach $comments as $c}
                <tr>
                    <td>{$c.created_at|date_format:"%Y-%m-%d %H:%M"}</td>
                    <td><strong>{$c.first_name|escape}</strong></td>
                    <td><a href="/product/{$c.product_id}" target="_blank">{$c.product_name|escape}</a></td>
                    <td>{$c.content|escape|nl2br}</td>
                    <td class="text-center">
                        <form action="/admin/comments/approve/{$c.id}" method="POST" class="d-inline">
                            <button type="submit" class="btn-success btn-sm">Approve</button>
                        </form>
                        <form action="/admin/comments/delete/{$c.id}" method="POST" class="d-inline" onsubmit="return confirm('Reject and delete this comment?');">
                            <button type="submit" class="btn-danger btn-sm mt-1">Reject</button>
                        </form>
                    </td>
                </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
</main>
</body>
</html>