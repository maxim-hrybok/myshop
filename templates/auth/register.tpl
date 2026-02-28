{include file='partials/header.tpl'}

<main class="auth-container">
    <h2>Create an Account</h2>

    {* Display an error message if one exists *}
    {if isset($error)}
        <p class="error-message">{$error}</p>
    {/if}

    <form action="/register" method="POST">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="button-primary">Register</button>
    </form>
    <p>Already have an account? <a href="/login">Login here</a></p>
</main>