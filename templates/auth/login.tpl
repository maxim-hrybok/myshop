{include file='partials/header.tpl'}

<main class="auth-container">
    <h2>Login to Your Account</h2>

    {* Display an error message if one exists *}
    {if isset($error)}
        <p class="error-message">{$error}</p>
    {/if}

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <form action="/login" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="g-recaptcha" data-sitekey="{$recaptcha_site_key}" style="margin-bottom: 15px;"></div>
        
        <button type="submit" class="button-primary">Login</button>
    </form>
    <p>Don't have an account? <a href="/register">Register here</a></p>
</main>

