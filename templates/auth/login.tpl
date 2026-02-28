{include file='partials/header.tpl'}

<main class="auth-container">
    <h2>Login to Your Account</h2>

    {* Display an error message if one exists *}
    {if isset($error)}
        <p class="error-message">{$error}</p>
    {/if}

    <form action="/login" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="button-primary">Login</button>
    </form>
    <p>Don't have an account? <a href="/register">Register here</a></p>
</main>

